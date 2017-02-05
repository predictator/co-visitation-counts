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
		$coVisit->addVisit($this->getVisit(null, $visit->getObjectId()));
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

		$result = $coVisit->getResult($this->getVisit(null, $visit1->getObjectId()));
		$this->assertEquals(
			array(
				$visit2->getObjectId()
			),
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

		$result = $coVisit->getResult($this->getVisit(null, $visit2->getObjectId()));
		$this->assertEquals(
			array(
				$visit1->getObjectId()
			),
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

		$result = $coVisit->getResult($this->getVisit(null, $visit1->getObjectId()));
		$this->assertEquals(
			array(
				$visit2->getObjectId()
			),
			$result
		);
	}

	/**
	 * @test
	 */
	public function already_visited()
	{
		$coVisit = new CoVisitationCounts();
		$visit1 = $this->getVisit();
		$visit2 = $this->getVisit($visit1->getUserId());
		$coVisit->addVisit($visit1);
		$coVisit->addVisit($visit2);

		$visit3 = $this->getVisit(null, $visit1->getObjectId());
		$coVisit->addVisit($visit3);
		$visit4 = $this->getVisit($visit3->getUserId());
		$coVisit->addVisit($visit4);


		$result = $coVisit->getResult($this->getVisit($visit1->getUserId(), $visit1->getObjectId()));
		$this->assertEquals(
			array(
				$visit4->getObjectId()
			),
			$result
		);
	}

	/**
	 * @param null|string $uuid
	 * @param null|string $objectId
	 * @return CoVisitationCounts\Visit
	 */
	protected function getVisit($uuid = null, $objectId = null)
	{
		$generator = Factory::create();
		if (!$uuid) {
			$uuid = $generator->uuid;
		}

		if (!$objectId) {
			$objectId = (string) rand(1, 9999999999999999);
		}
		return new CoVisitationCounts\Visit($uuid, $objectId);
	}
}
