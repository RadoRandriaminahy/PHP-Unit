<?php

namespace Tests\AppBundle;

use DateTime;
use Throwable;
use AppBundle\Entity\Project;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ProjectsControllerTest extends WebTestCase
{

	private $container;
	private $client;
	private $em;
	private $crawler;


	public function setUp()
	{
		parent::setUp();

		$this->client = static::createClient();

		$this->container = $this->client->getContainer();

		$this->em = $this->container->get('doctrine')->getManager();

		$schematool = new SchemaTool($this->em);

		$schematool->dropDatabase();

		static $metadatas;
		if(is_null($metadatas))
		{
			$metadatas = $this->em->getMetadataFactory()->getAllMetadata();
		}

		if(!empty($metadatas))
		{
			$schematool->createSchema($metadatas);
		}

	}

	public function test_index_should_list_all_projects()
	{

		$project1 = new Project;

		$project1->SetName('Project 1')
					->setDescription('Best Project 1 in the world')
					->setWebsite('www.projet1.com')
					->setExpiredOn(new DateTime('+ 4 years'))
					->setTargetAmount(10);

		$project2 = new Project;

		$project2->SetName('Project 2')
					->setDescription('Best Project 2 in the world')
					->setWebsite('www.projet2.com')
					->setExpiredOn(new DateTime('+ 1 years'))
					->setTargetAmount(5);

		$project3 = new Project;

		$project3->SetName('Project 3')
					->setDescription('Best Project 3 in the world')
					->setWebsite('www.projet3.com')
					->setExpiredOn(new DateTime('+ 60 days'))
					->setTargetAmount(20);

		$this->em->persist($project1);
		$this->em->persist($project2);
		$this->em->persist($project3);

		$this->em->flush();

		$this->crawler = $this->client->request('GET', '/projects');

		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());

		$this->assertContains('3 Projects', $this->client->getResponse()->getContent());
		$this->assertContains($project1->getName(), $this->client->getResponse()->getContent());
		$this->assertContains($project2->getName(), $this->client->getResponse()->getContent());
		$this->assertContains($project3->getName(), $this->client->getResponse()->getContent());

	}

	protected function onNotSuccessfulTest(Throwable $t)
	{

		if($this->crawler && $this->crawler->filter('.exception-message')->count() > 0)
		{
			$throwableclass = get_class($t);

			throw new $throwableclass($t->getMessage() . ' | ' . $this->crawler->filter('.exception-message')->text());
		}
	}

	public function tearDown()
	{
		parent::tearDown();

		$this->em->close();
		$this->em=null;
	}
}
