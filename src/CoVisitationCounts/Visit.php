<?php

namespace Predictator\CoVisitationCounts;


class Visit implements VisitInterface
{

	/**
	 * @var string
	 */
	private $userId;

	/**
	 * @var VisitedObjectInterface
	 */
	private $visitedObject;

	/**
	 * @param string $userId
	 * @param VisitedObjectInterface $visitedObject
	 */
	public function __construct(string $userId, VisitedObjectInterface $visitedObject)
	{
		$this->userId = $userId;
		$this->visitedObject = $visitedObject;
	}

	/**
	 * @return string
	 */
	public function getUserId(): string
	{
		return $this->userId;
	}

	/**
	 * @return VisitedObjectInterface
	 */
	public function getVisitedObject(): VisitedObjectInterface
	{
		return $this->visitedObject;
	}
}