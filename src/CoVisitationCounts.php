<?php

namespace Predictator;

use Predictator\CoVisitationCounts\CoVisitationCountsModelInterface;
use Predictator\CoVisitationCounts\ResultSet;
use Predictator\CoVisitationCounts\VisitedObjectInterface;
use Predictator\CoVisitationCounts\VisitInterface;

class CoVisitationCounts
{
	/**
	 * @var array
	 */
	protected $result = [];

	/**
	 * @var array
	 */
	private $userVisits = [];

	/**
	 * @var VisitedObjectInterface[]
	 */
	private $visitedObjects = [];

	/**
	 * @var int
	 */
	protected $visitationTrackDeep = 3;

	/**
	 * @param VisitInterface $visit
	 */
	public function addVisit(VisitInterface $visit)
	{
		if (!isset($this->userVisits[$visit->getUserId()])) {
			$this->userVisits[$visit->getUserId()] = [];
		}
		$this->userVisits[$visit->getUserId()][] = $visit->getVisitedObject()->getId();

		if (!isset($this->visitedObjects[$visit->getVisitedObject()->getId()])) {
			$this->visitedObjects[$visit->getVisitedObject()->getId()] = $visit->getVisitedObject();
		}

	}

	/**
	 * @param VisitInterface $visit
	 * @return ResultSet
	 */
	public function getResult(VisitInterface $visit) : ResultSet
	{
		$result = $this->processResult();

		$visitId = $visit->getVisitedObject()->getId();
		$resultSet = new ResultSet();

		if (isset($result[$visitId])) {

			foreach (array_keys($result[$visitId]) as $objectId) {
				$resultSet->addVisitedObject($this->visitedObjects[$objectId]);
			}
		}

		return $resultSet;
	}

	/**
	 * @param string $first
	 * @param string $next
	 * @param int $withNumber
	 */
	private function increaseCoVisionCounts(string $first, string $next, $withNumber = 1)
	{
		if (!isset($this->result[$first])) {
			$this->result[$first] = [];
		}

		if (!isset($this->result[$first][$next])) {
			$this->result[$first][$next] = 0;
		}

		$this->result[$first][$next] += $withNumber;
	}

	/**
	 * @param CoVisitationCountsModelInterface $coVisitationCountsModel
	 * @return CoVisitationCountsModelInterface
	 */
	public function exportModel(CoVisitationCountsModelInterface $coVisitationCountsModel) :CoVisitationCountsModelInterface
	{
		$result = $this->processResult();
		foreach ($result as $visitId => $item) {
			$resultSet = new ResultSet();
			foreach (array_keys($result[$visitId]) as $objectId) {
				$resultSet->addVisitedObject($this->visitedObjects[$objectId]);
			}
			$coVisitationCountsModel->addResult($visitId, $resultSet);
		}
		return $coVisitationCountsModel;
	}

	/**
	 * @return array
	 */
	private function processResult(): array
	{
		$visitLog = [];
		foreach ($this->userVisits as $userId => $visitList) {
			foreach ($visitList as $item) {

				$visitLogLength = count($visitLog);
				if ($visitLogLength < 1) {
					$visitLog[] = $item;
					continue;
				}

				$last = end($visitLog);
				if ($last == $item) {
					continue;
				}

				$iteration = 0;
				while ($visitLogLength > $this->visitationTrackDeep) {
					$lastLast = array_shift($visitLog);
					$score = $this->visitationTrackDeep - ++$iteration;
					$this->increaseCoVisionCounts($item, $lastLast, $score);
					$this->increaseCoVisionCounts($lastLast, $item, $score);
				}

				$this->increaseCoVisionCounts($last, $item, $this->visitationTrackDeep);
				$this->increaseCoVisionCounts($item, $last, $this->visitationTrackDeep);
			}
		}

		foreach ($this->result as &$item) {
			arsort($item);
		}

		return $this->result;
	}

}