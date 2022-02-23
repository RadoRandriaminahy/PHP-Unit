<?php

namespace Tests\AppBundle\Framework;

use Throwable;
use PHPUnit\Framework\Exception;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase As BaseWebtestCase;
//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class WebTestCase extends BaseWebtestCase
{
    protected $client;

    protected $container;

    protected $em;

    protected $crawler;

    protected $response;

    protected $responseContent;

    protected function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();

        $this->container = $this->client->getContainer();

        $this->em = $this->getDoctrine()->getManager();

        static $metadatas;

        if(! isset($metadatas))
        {
          $metadatas = $this->em->getMetadataFactory()->getAllMetadata();
        }

        $schematools = new SchemaTool($this->em);

        $schematools->dropDatabase();

        if(!empty($metadatas))
        {
          $schematools->createSchema($metadatas);
        }

    }

    protected function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

    protected function getParameter($param)
    {
        return $this->container->getParameter($param);
    }

    public function visit($url)
    {
        $this->crawler = $this->client->request('GET', $url);

        $this->response = $this->client->getResponse();
        $this->responseContent = $this->response->getContent();

        return $this;
    }

    public function seeText($text)
    {

        $this->assertContains( $text, $this->responseContent );

        return $this;
    }

    public function assertResponseOK()
    {
        $this->assertEquals(200, $this->response->getStatusCode());

        return $this;
    }

    protected function onNotSuccessfulTest(Throwable $t)
    {

        if($this->crawler && $this->crawler->filter('.exception-message')->count() > 0)
        {
            $throwableclass = get_class($t);

            $message = $this->crawler->filter('.exception-message')->eq(0)->text();

            throw new $throwableclass($t->getMessage() . ' | ' . $message);
        }

        throw $t;
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em=null;
    }
}
