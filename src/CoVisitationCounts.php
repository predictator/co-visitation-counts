<?php

namespace Predictator;

use Predictator\CoVisitationCounts\CoVisitationCountsModelInterface;
use Predictator\CoVisitationCounts\ResultSet;
use Predictator\CoVisitationCounts\Visit;
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
		$this->userVisits[$visit->getUserId()][] = $visit;

		if (!isset($this->visitedObjects[$visit->getVisitedObject()->getId()])) {
			$this->visitedObjects[$visit->getVisitedObject()->getId()] = $visit->getVisitedObject();
		}
	}

	/**
	 * @param VisitInterface $visit
	 * @return ResultSet
	 */
	public function getResult(VisitInterface $visit): ResultSet
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
	 * @param VisitInterface $first
	 * @param VisitInterface $next
	 * @param int $score
	 */
	private function increaseCoVisionCounts(VisitInterface $first, VisitInterface $next, $score = 1)
	{
		if (!$score) {
			return;
		}

		$score -= $this->calculateScore($next, $first);

		$firstId = $first->getVisitedObject()->getId();
		if (!isset($this->result[$firstId])) {
			$this->result[$firstId] = [];
		}

		$nextId = $next->getVisitedObject()->getId();
		if (!isset($this->result[$firstId][$nextId])) {
			$this->result[$firstId][$nextId] = 0;
		}

		$this->result[$firstId][$nextId] += $score;
	}

	/**
	 * @param CoVisitationCountsModelInterface $coVisitationCountsModel
	 * @return CoVisitationCountsModelInterface
	 */
	public function exportModel(CoVisitationCountsModelInterface $coVisitationCountsModel): CoVisitationCountsModelInterface
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

		foreach ($this->userVisits as $userId => $visitList) {
			$visitLog = [];
			/** @var Visit $visit */
			foreach ($visitList as $visit) {

				$visitLogLength = count($visitLog);
				if ($visitLogLength < 1) {
					$visitLog[] = $visit;
					continue;
				}

				$last = end($visitLog);
				if ($last == $visit) {
					continue;
				}

				$iteration = 0;

				// truncate visit log
				$visitLog = array_slice($visitLog, -$this->visitationTrackDeep);
				foreach ($visitLog as $lastVisit) {
					$score = $this->visitationTrackDeep - ++$iteration;
					$this->increaseCoVisionCounts($visit, $lastVisit, $score);
					$this->increaseCoVisionCounts($lastVisit, $visit, $score);
				}

				$this->increaseCoVisionCounts($last, $visit, $this->visitationTrackDeep);
				$this->increaseCoVisionCounts($visit, $last, $this->visitationTrackDeep);
				$visitLog[] = $visit;
			}
		}

		foreach ($this->result as &$item) {
			arsort($item);
		}

		return $this->result;
	}

	/**
	 * @param VisitInterface $base
	 * @param VisitInterface $previous
	 * @return float
	 */
	private function calculateScore(VisitInterface $base, VisitInterface $previous) :float
	{
		$diff = $base->getVisitTime()->diff($previous->getVisitTime());
		if ($diff->y) {
			return 0;
		}
		$f = (365 - $diff->days) / 365;
		if (!$diff->invert) {
			$f = -abs($f);
		}
		return $f;
	}

}