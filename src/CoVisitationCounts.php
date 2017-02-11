<?php

namespace Predictator;

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
		$this->userVisits[$visit->getUserId()][] = $visit->getVisitedObject();

		if (!isset($this->visitedObjects[$visit->getVisitedObject()->getId()])) {
			$this->visitedObjects[$visit->getVisitedObject()->getId()] = $visit->getVisitedObject();
		}

	}

	/**
	 * @param VisitInterface $visit
	 * @return VisitedObjectInterface[]
	 */
	public function getResult(VisitInterface $visit) :array
	{
		$result = [];

		$visitId = $visit->getVisitedObject()->getId();

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

		if (isset($result[$visitId])) {
			$return = [];

			foreach (array_keys($result[$visitId]) as $objectId) {
				$return[] = $this->visitedObjects[$objectId];
			}
			return $return;
		}

		return array();
	}

	/**
	 * @param VisitedObjectInterface $first
	 * @param VisitedObjectInterface $next
	 * @param array $result
	 */
	private function increaseCoVisionCounts(VisitedObjectInterface $first, VisitedObjectInterface $next, array &$result)
	{
		$firstId = $first->getId();
		if (!isset($result[$firstId])) {
			$result[$firstId] = [];
		}

		$nextId = $next->getId();
		if (!isset($result[$firstId][$nextId])) {
			$result[$firstId][$nextId] = 0;
		}

		$result[$firstId][$nextId]++;
	}

}