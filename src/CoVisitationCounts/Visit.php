<?php

namespace Predictator\CoVisitationCounts;


class Visit implements VisitInterface
{

	/**
	 * @var string
	 */
	private $userId;
	/**
	 * @var string
	 */
	private $objectId;

	/**
	 * @param string $userId
	 * @param string $objectId
	 */
	public function __construct(string $userId, string $objectId)
	{
		$this->userId = $userId;
		$this->objectId = $objectId;
	}

	/**
	 * @return string
	 */
	public function getUserId(): string
	{
		return $this->userId;
	}

	/**
	 * @return string
	 */
	public function getObjectId(): string
	{
		return $this->objectId;
	}
}