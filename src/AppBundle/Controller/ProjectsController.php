<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ProjectsController extends AbstractController
{

  /**
   * @Route("/projects", name="projects_path")
   */
	public function indexAction()
	{

    $projects = $this->getDoctrine()->getRepository('AppBundle:Project')->findAll();

		return $this->render('projects/index.html.twig', compact('projects'));

	}
}
