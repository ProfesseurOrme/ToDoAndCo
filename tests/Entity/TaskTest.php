<?php

	namespace App\Tests\Entity;

	use App\Entity\Task;
	use App\Entity\User;
	use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

	class TaskTest extends KernelTestCase {

		public function getEntityUser() : User {
			return (new User())
				->setUsername("testPseudo")
				->setRoles(["ROLE_USER"])
				->setPassword("testPassword")
				->setEmail("test@mail.fr")
			;
		}

		public function getEntity() : Task {
			return (new Task())
				->setCreatedAt(new \DateTime())
				->setTitle("Test de titre")
				->setContent("Test de contenu de la tâche")
				->setIsDone(false)
				->setUser($this->getEntityUser())
			;
		}

		public function assertHasErrors(Task $task, int $nbErrors = 0) {
			self::bootKernel();

			$errors = self::$container->get("validator")->validate($task);

			$this->assertCount($nbErrors, $errors);
		}

		public function testValidEntity() {
			$this->assertHasErrors($this->getEntity());
		}

		public function testInvalidEntity() {
			$task = $this->getEntity()
				->setTitle((""))
				->setContent("")
			;
			$this->assertHasErrors($task, 2);
		}
	}