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
	public function visited_2nd()
	{
		$coVisit = new CoVisitationCounts();
		$visit1 = $this->getVisit();
		$visit2 = $this->getVisit($visit1->getUserId());
		$visit3 = $this->getVisit($visit1->getUserId());
		$coVisit->addVisit($visit1);
		$coVisit->addVisit($visit2);
		$coVisit->addVisit($visit3);

		$result = $coVisit->getResult($this->getVisit(null, $visit1->getVisitedObject()->getId()));
		$this->assertEquals(
			$this->getResultSet(
				array(
					$visit2->getVisitedObject(),
					$visit3->getVisitedObject(),
				)
			),
			$result
		);
	}

	/**
	 * @test
	 */
	public function visited_3rd()
	{
		$coVisit = new CoVisitationCounts();
		$visit1 = $this->getVisit();
		$visit2 = $this->getVisit($visit1->getUserId());
		$visit3 = $this->getVisit($visit1->getUserId());
		$visit4 = $this->getVisit($visit1->getUserId());
		$coVisit->addVisit($visit1);
		$coVisit->addVisit($visit2);
		$coVisit->addVisit($visit3);
		$coVisit->addVisit($visit4);

		$result = $coVisit->getResult($this->getVisit(null, $visit1->getVisitedObject()->getId()));
		$this->assertEquals(
			$this->getResultSet(
				array(
					$visit2->getVisitedObject(),
					$visit3->getVisitedObject(),
					$visit4->getVisitedObject(),
				)
			),
			$result
		);
	}

	/**
	 * @test
	 */
	public function visited_4th()
	{
		$coVisit = new CoVisitationCounts();

		$visit1a = $this->getVisit();
		$visit2a = $this->getVisit($visit1a->getUserId());
		$visit3a = $this->getVisit($visit1a->getUserId());
		$visit4a = $this->getVisit($visit1a->getUserId());
		$visit5a = $this->getVisit($visit1a->getUserId());
		$coVisit->addVisit($visit1a);
		$coVisit->addVisit($visit2a);
		$coVisit->addVisit($visit3a);
		$coVisit->addVisit($visit4a);
		$coVisit->addVisit($visit5a);

		$visit1b = $this->getVisit();
		$visit2b = $this->getVisit($visit1b->getUserId());
		$visit3b = $this->getVisit($visit1b->getUserId());
		$visit4b = $this->getVisit($visit1b->getUserId());
		$visit5b = $this->getVisit($visit1b->getUserId());
		$coVisit->addVisit($visit4b);
		$coVisit->addVisit($visit5b);
		$coVisit->addVisit($visit2b);
		$coVisit->addVisit($visit3b);
		$coVisit->addVisit($visit1b);

		$result = $coVisit->getResult($this->getVisit(null, $visit1a->getVisitedObject()->getId()));
		$this->assertEquals(
			$this->getResultSet(
				array(
					$visit2a->getVisitedObject(),
					$visit3a->getVisitedObject(),
					$visit4a->getVisitedObject(),
				)
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
	 * @test
	 */
	public function visitByDefaultNow()
	{
		$visitedObject = new CoVisitationCounts\VisitedObject('foo');
		$visit = new CoVisitationCounts\Visit(10, $visitedObject);
		$dateTime = new \DateTime();
		$this->assertEquals($dateTime->getTimestamp(), $visit->getVisitTime()->getTimestamp());
	}

	/**
	 * @test
	 */
	public function visitTimeObject()
	{
		$visitedObject = new CoVisitationCounts\VisitedObject('foo');
		$dateTime = new \DateTime();
		$visit = new CoVisitationCounts\Visit(10, $visitedObject, $dateTime);
		$this->assertEquals($dateTime, $visit->getVisitTime());
	}

	/**
	 * @test
	 * @dataProvider timeStringsDataProvider
	 * @param string $timeDiff
	 */
	public function TimePriorities($timeDiff)
	{
		$time = new \DateTime($timeDiff);
		$coVisit = new CoVisitationCounts();
		$visit1a = $this->getVisit(null, 'visit1');
		$visit2a = $this->getVisit($visit1a->getUserId(), 'visit2');
		$visit3a = $this->getVisit($visit1a->getUserId(), 'visit3', $time);
		$visit4a = $this->getVisit($visit1a->getUserId(), 'visit4');

		$coVisit->addVisit($visit1a);
		$coVisit->addVisit($visit2a);
		$coVisit->addVisit($visit3a);
		$coVisit->addVisit($visit4a);

		$visit1b = $this->getVisit(null, $visit1a->getVisitedObject()->getId());
		$visit2b = $this->getVisit($visit1b->getUserId(), $visit2a->getVisitedObject()->getId());
		$visit3b = $this->getVisit($visit1b->getUserId(), $visit3a->getVisitedObject()->getId(), $time);
		$visit4b = $this->getVisit($visit1b->getUserId(), $visit4a->getVisitedObject()->getId());

		$coVisit->addVisit($visit1b);
		$coVisit->addVisit($visit2b);
		$coVisit->addVisit($visit3b);
		$coVisit->addVisit($visit4b);

		$result = $coVisit->getResult($this->getVisit(null, $visit1a->getVisitedObject()->getId()));

		$this->assertEquals(
			$this->getResultSet(
				array(
					$visit2a->getVisitedObject(),
					$visit4a->getVisitedObject(),
					$visit3a->getVisitedObject()
				)
			),
			$result
		);
	}

	/**
	 * return array
	 */
	public function timeStringsDataProvider()
	{
		return [
			['last week'],
			['-3 weeks'],
			['-5 weeks'],
			['last month'],
			['-2 months'],
			['last year'],
			['-2 years'],
		];
	}

	/**
	 * @param null|string $uuid
	 * @param null|string $objectId
	 * @param \DateTime $dateTime
	 * @return CoVisitationCounts\Visit
	 */
	protected function getVisit(string $uuid = null, string $objectId = null, \DateTime $dateTime = null)
	{
		$generator = Factory::create();
		if (!$uuid) {
			$uuid = $generator->uuid;
		}

		if (!$objectId) {
			$objectId = (string) rand(1, 9999999999999999);
		}

		$visitedObject = new CoVisitationCounts\VisitedObject($objectId);

		return new CoVisitationCounts\Visit($uuid, $visitedObject, $dateTime);
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
