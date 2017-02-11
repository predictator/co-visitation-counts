<?php

namespace Predictator\CoVisitationCounts;


class VisitedObject implements VisitedObjectInterface
{

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @param string $id
	 */
	public function __construct(string $id)
	{
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}
}