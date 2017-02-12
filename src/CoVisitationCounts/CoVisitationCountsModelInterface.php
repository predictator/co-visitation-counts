<?php

namespace Predictator\CoVisitationCounts;


interface CoVisitationCountsModelInterface
{

	/**
	 * @param VisitInterface $visit
	 * @return ResultSet
	 */
	public function getResult(VisitInterface $visit) :ResultSet;

	/**
	 * @param string $visitId
	 * @param ResultSet $resultSet
	 * @return void
	 */
	public function addResult(string $visitId, ResultSet $resultSet);

}