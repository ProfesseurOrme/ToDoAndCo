<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{
	/**
	* @Route("/login", name="login")
	* @param AuthenticationUtils $authenticationUtils
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function login(AuthenticationUtils $authenticationUtils)
	{

		$error = $authenticationUtils->getLastAuthenticationError();
		$lastUsername = $authenticationUtils->getLastUsername();


		return $this->render('security/login.html.twig', array(
			'last_username' => $lastUsername,
			'error'         => $error,
		));
	}

	/**
	* @Route("/login_check", name="login_check")
	 * @codeCoverageIgnore
	*/
	public function loginCheck()
	{
		// This code is never executed.
	}

	/**
	* @Route("/logout", name="logout")
	 * @codeCoverageIgnore
	*/
	public function logoutCheck()
	{
		// This code is never executed.
	}
}
