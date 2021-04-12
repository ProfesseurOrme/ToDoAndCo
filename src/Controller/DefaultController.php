<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class DefaultController extends AbstractController
{
	/**
	* @Route("/", name="homepage")
	*/
	public function index(EntityManagerInterface $entityManager)
	{

		return $this->render('default/index.html.twig');
	}
}
