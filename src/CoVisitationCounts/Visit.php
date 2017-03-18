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
	 * @var \DateTime
	 */
	private $time;

	/**
	 * @param string $userId
	 * @param VisitedObjectInterface $visitedObject
	 * @param \DateTimeInterface $time
	 */
	public function __construct(string $userId, VisitedObjectInterface $visitedObject, \DateTimeInterface  $time = null)
	{
		$this->userId = $userId;
		$this->visitedObject = $visitedObject;
		if (!$time) {
			$time = new \DateTime();
		}
		$this->time = $time;
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

	/**
	 * @return \DateTimeInterface
	 */
	public function getVisitTime() : \DateTimeInterface
	{
		return $this->time;
	}
}