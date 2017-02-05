<?php

namespace Predictator\CoVisitationCounts;


interface VisitInterface
{

	/**
	 * @return string
	 */
	public function getUserId() : string;

	/**
	 * @return string
	 */
	public function getObjectId() : string;
}