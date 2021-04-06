<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{
	private $manager;

	public function __construct(EntityManagerInterface $manager) {
		$this->manager = $manager;
	}
	/**
	* @Route("/tasks", name="task_list")
	*/
	public function listTask()
	{
		return $this->render('task/list.html.twig', [
			'tasks' => $this->getDoctrine()->getRepository('App:Task')->findAll()
		]);
	}

	/**
	* @Route("/tasks/create", name="task_create")
	* @param Request $request
	* @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	*/
	public function createTask(Request $request)
	{
		$task = new Task();
		$form = $this->createForm(TaskType::class, $task);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$task->setCreatedAt(new \Datetime());
			$task->setIsDone(false);
			$task->setUser($this->getUser());

			$this->manager->persist($task);
			$this->manager->flush();

			$this->addFlash('success', 'La tâche a été bien été ajoutée.');

			return $this->redirectToRoute('task_list');
		}

		return $this->render('task/create.html.twig', [
			'form' => $form->createView()
		]);
    }

	/**
	* @Route("/tasks/{id}/edit", name="task_edit")
	* @param Task $task
	* @param Request $request
	* @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	*/
	public function editTask(Task $task, Request $request)
	{
		$form = $this->createForm(TaskType::class, $task);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$this->manager->flush();

			$this->addFlash('success', 'La tâche a bien été modifiée.');

			return $this->redirectToRoute('task_list');
		}

		return $this->render('task/edit.html.twig', [
			'form' => $form->createView(),
			'task' => $task,
		]);
	}

	/**
	* @Route("/tasks/{id}/toggle", name="task_toggle")
	* @param Task $task
	* @return \Symfony\Component\HttpFoundation\RedirectResponse
	*/
	public function toggleTask(Task $task)
	{
		$task->setIsDone(!$task->getIsDone());
		$this->manager->flush();

		$this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

		return $this->redirectToRoute('task_list');
	}

	/**
	* @Route("/tasks/{id}/delete", name="task_delete")
	* @param Task $task
	* @return \Symfony\Component\HttpFoundation\RedirectResponse
	*/
	public function deleteTask(Task $task)
	{
		$this->manager->remove($task);
		$this->manager->flush();

		$this->addFlash('success', 'La tâche a bien été supprimée.');

		return $this->redirectToRoute('task_list');
	}
}
