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
	private $userVisits = [];

	/**
	 * @var VisitedObjectInterface[]
	 */
	private $visitedObjects = [];

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
	 * @param array $result
	 */
	private function increaseCoVisionCounts(string $first, string $next, array &$result)
	{
		if (!isset($result[$first])) {
			$result[$first] = [];
		}

		if (!isset($result[$first][$next])) {
			$result[$first][$next] = 0;
		}

		$result[$first][$next]++;
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
		$result = [];

		$last = null;
		foreach ($this->userVisits as $userId => $visitList) {
			foreach ($visitList as $item) {

				if (!$last) {
					$last = $item;
					continue;
				}

				if ($last == $item) {
					continue;
				}

				$this->increaseCoVisionCounts($last, $item, $result);
				$this->increaseCoVisionCounts($item, $last, $result);

				$last = $item;
			}
		}

		foreach ($result as &$item) {
			arsort($item);
		}

		return $result;
	}

}