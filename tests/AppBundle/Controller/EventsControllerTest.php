<?php

namespace Tests\AppBundle\Controller;

use Datetime;
use AppBundle\Entity\Event;
use Tests\AppBundle\Framework\WebTestCase;
//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class EventsControllerTest extends WebTestCase
{
	/** @test */
	public function index_should_list_all_events()
	{

		$event1 = new Event;
		$event1->setName('Symfony course')
				->setLocation('Paris, FR')
				->setPrice(100)
				->setDescription('Best Symfony course ever')
				->setStartsAt(new Datetime('+ 40 days'));

		$event2 = new Event;
		$event2->setName('Lavarel course')
				->setLocation('Toronto, USA')
				->setPrice(0)
				->setDescription('Best Lavarel course ever')
				->setStartsAt(new Datetime('+ 5 years'));

		$event3 = new Event;
		$event3->setName('SQL course')
				->setLocation('Quebec, CA')
				->setPrice(50)
				->setDescription('Best SQL course ever')
				->setStartsAt(new Datetime('+ 10 days'));

		$this->em->persist($event1);
		$this->em->persist($event2);
		$this->em->persist($event3);

		$this->em->flush();

		$this->visit('/events')
			->assertResponseOK()
			->seeText('3 Events')
			->seeText($event1->getName())
			->seeText($event1->getDescription())
			->seeText($event1->getLocation())
			->seeText($event1->getStartsat()->format($this->getParameter('date_format_default')))
			->seeText('$100')
			->seeText($event2->getName())
			->seeText($event3->getName())
		;

	}

}
