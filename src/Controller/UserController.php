<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
	/**
	* @Route("/users", name="user_list")
	*/
	public function listUsers()
	{
		return $this->render('user/list.html.twig', [
				'users' => $this->getDoctrine()->getRepository('App:User')->findAll()
		]);
	}

	/**
	* @Route("/users/create", name="user_create")
	* @param Request $request
	* @param UserPasswordEncoderInterface $passwordEncoder
	* @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	*/
	public function createUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
	{
		$user = new User();
		$form = $this->createForm(UserType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$password = $passwordEncoder->encodePassword($user, $user->getPassword());
			$user->setPassword($password);

			$em->persist($user);
			$em->flush();

			$this->addFlash('success', "L'utilisateur a bien été ajouté.");

			return $this->redirectToRoute('user_list');
		}

		return $this->render('user/create.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	* @Route("/users/{id}/edit", name="user_edit")
	* @param User $user
	* @param Request $request
	* @param UserPasswordEncoderInterface $passwordEncoder
	* @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	*/
	public function editUser(User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder)
	{
		$form = $this->createForm(UserType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
				$password = $passwordEncoder->encodePassword($user, $user->getPassword());
				$user->setPassword($password);

				$this->getDoctrine()->getManager()->flush();

				$this->addFlash('success', "L'utilisateur a bien été modifié");

				return $this->redirectToRoute('user_list');
		}

		return $this->render('user/edit.html.twig', [
			'form' => $form->createView(),
			'user' => $user
		]);
	}
}
