<?php

namespace Tests\Predictator\Unit;


use Faker\Factory;
use Predictator\CoVisitationCounts;


class CoVisitationCountsTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function oneUserOneVisit()
	{
		$coVisit = new CoVisitationCounts();
		$coVisit->addVisit($this->getVisit());
		$this->assertEmpty(
			$coVisit->getResult($this->getVisit())
		);
	}

	/**
	 * @test
	 */
	public function oneUserTwoVisit()
	{
		$coVisit = new CoVisitationCounts();
		$visit = $this->getVisit();
		$coVisit->addVisit($visit);
		$coVisit->addVisit($this->getVisit($visit->getUserId()));
		$this->assertEmpty(
			$coVisit->getResult($this->getVisit())
		);
	}

	/**
	 * @test
	 */
	public function twoUserOneObject()
	{
		$coVisit = new CoVisitationCounts();
		$visit = $this->getVisit();
		$coVisit->addVisit($visit);
		$coVisit->addVisit($this->getVisit(null, $visit->getVisitedObject()->getId()));
		$this->assertEmpty(
			$coVisit->getResult($this->getVisit())
		);
	}

	/**
	 * @test
	 */
	public function twoProduct_next()
	{
		$coVisit = new CoVisitationCounts();
		$visit1 = $this->getVisit();
		$visit2 = $this->getVisit($visit1->getUserId());
		$coVisit->addVisit($visit1);
		$coVisit->addVisit($visit2);

		$result = $coVisit->getResult($this->getVisit(null, $visit1->getVisitedObject()->getId()));
		$this->assertEquals(
			$this->getResultSet(array($visit2->getVisitedObject())),
			$result
		);
	}

	/**
	 * @test
	 */
	public function twoProduct_prev()
	{
		$coVisit = new CoVisitationCounts();
		$visit1 = $this->getVisit();
		$visit2 = $this->getVisit($visit1->getUserId());
		$coVisit->addVisit($visit1);
		$coVisit->addVisit($visit2);

		$result = $coVisit->getResult($this->getVisit(null, $visit2->getVisitedObject()->getId()));
		$this->assertEquals(
			$this->getResultSet(array($visit1->getVisitedObject())),
			$result
		);
	}

	/**
	 * @test
	 */
	public function sameVisit_clear()
	{
		$coVisit = new CoVisitationCounts();
		$visit1 = $this->getVisit();
		$visit2 = $this->getVisit($visit1->getUserId());
		$coVisit->addVisit($visit1);
		$coVisit->addVisit($visit1);
		$coVisit->addVisit($visit2);

		$result = $coVisit->getResult($this->getVisit(null, $visit1->getVisitedObject()->getId()));
		$resultSet = new CoVisitationCounts\ResultSet();
		$resultSet->addVisitedObject($visit2->getVisitedObject());
		$this->assertEquals($resultSet, $result);
	}

	/**
	 * @test
	 */
	public function CoVisitModel()
	{
		$coVisit = new CoVisitationCounts();
		$visit1 = $this->getVisit();
		$visit2 = $this->getVisit($visit1->getUserId());
		$coVisit->addVisit($visit1);
		$coVisit->addVisit($visit2);

		$model = $coVisit->exportModel(new CoVisitationCounts\CoVisitationCountsModel());
		$this->assertInstanceOf(CoVisitationCounts\CoVisitationCountsModelInterface::class, $model);

		$result = $model->getResult($this->getVisit(null, $visit1->getVisitedObject()->getId()));
		$this->assertEquals(
			$this->getResultSet(array($visit2->getVisitedObject())),
			$result
		);
	}

	/**
	 * @test
	 */
	public function CoVisitModel_empty()
	{
		$coVisit = new CoVisitationCounts();
		$visit1 = $this->getVisit();
		$visit2 = $this->getVisit($visit1->getUserId());
		$coVisit->addVisit($visit1);
		$coVisit->addVisit($visit2);

		$model = $coVisit->exportModel(new CoVisitationCounts\CoVisitationCountsModel());
		$this->assertInstanceOf(CoVisitationCounts\CoVisitationCountsModelInterface::class, $model);

		$result = $model->getResult($this->getVisit());
		$this->assertEquals(
			new CoVisitationCounts\ResultSet(),
			$result
		);
	}

	/**
	 * @param null|string $uuid
	 * @param null|string $objectId
	 * @return CoVisitationCounts\Visit
	 */
	protected function getVisit(string $uuid = null, string $objectId = null)
	{
		$generator = Factory::create();
		if (!$uuid) {
			$uuid = $generator->uuid;
		}

		if (!$objectId) {
			$objectId = (string) rand(1, 9999999999999999);
		}

		$visitedObject = new CoVisitationCounts\VisitedObject($objectId);

		return new CoVisitationCounts\Visit($uuid, $visitedObject);
	}


	/**
	 * @param array $resultSetArray
	 * @return CoVisitationCounts\ResultSet
	 */
	protected function getResultSet(array $resultSetArray)
	{
		$resultSet = new CoVisitationCounts\ResultSet();
		foreach ($resultSetArray as $item) {
			$resultSet->addVisitedObject($item);
		}
		return $resultSet;
	}
}
