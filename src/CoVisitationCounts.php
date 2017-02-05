<?php

namespace Predictator;

use Predictator\CoVisitationCounts\VisitInterface;

class CoVisitationCounts
{
	/**
	 * @var array
	 */
	private $visits = [];

	/**
	 * @param VisitInterface $visit
	 */
	public function addVisit(VisitInterface $visit)
	{
		if (!isset($this->visits[$visit->getUserId()])) {
			$this->visits[$visit->getUserId()] = [];
		}
		$this->visits[$visit->getUserId()][] = $visit->getObjectId();

	}

	/**
	 * @param VisitInterface $visit
	 * @return array
	 */
	public function getResult(VisitInterface $visit) :array
	{
		$result = [];

		$last = null;
		foreach ($this->visits as $userId => $visitList) {
			foreach ($visitList as $item) {

				if (!$last) {
					$last = $item;
					continue;
				}

				$this->increaseCoVisionCounts($last, $item, $result);
				$this->increaseCoVisionCounts($item, $last, $result);

				$last = $item;
			}
		}

		$countTotal = 0;

		foreach ($result as &$item) {
			$countTotal += array_sum($item);
			arsort($item);
		}

		if (isset($result[$visit->getObjectId()])) {
			return array_keys($result[$visit->getObjectId()]);
		}

		return array();
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

}