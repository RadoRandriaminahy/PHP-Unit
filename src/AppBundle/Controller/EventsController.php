<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class EventsController extends AbstractController
{
    /**
     * @Route("/events", name="events_path")
     */
	public function indexAction()
	{

    $em = $this->getDoctrine()->getManager();

    $events = $em->getRepository(Event::class)->findAll();

		return $this->render('events/index.html.twig', compact('events'));
	}
}
