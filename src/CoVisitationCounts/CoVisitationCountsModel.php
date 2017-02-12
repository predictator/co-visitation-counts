<?php

namespace Predictator\CoVisitationCounts;


class CoVisitationCountsModel implements CoVisitationCountsModelInterface
{
	/**
	 * @var array
	 */
	private $result = [];

	/**
	 * @param VisitInterface $visit
	 * @return ResultSet
	 */
	public function getResult(VisitInterface $visit): ResultSet
	{
		$visitedObjectId = $visit->getVisitedObject()->getId();
		if (isset($this->result[$visitedObjectId])) {
			return $this->result[$visitedObjectId];
		}
		return new ResultSet();
	}

	/**
	 * @param string $visitId
	 * @param ResultSet $resultSet
	 * @return void
	 */
	public function addResult(string $visitId, ResultSet $resultSet)
	{
		$this->result[$visitId] = $resultSet;
	}
}