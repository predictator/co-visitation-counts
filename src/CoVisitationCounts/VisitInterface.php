<?php

namespace Predictator\CoVisitationCounts;


interface VisitInterface
{

	/**
	 * @return string
	 */
	public function getUserId() : string;

	/**
	 * @return VisitedObjectInterface
	 */
	public function getVisitedObject() : VisitedObjectInterface;
}