<?php

	namespace App\Tests\Entity;

	use App\Entity\Task;
	use App\Entity\User;
	use Doctrine\Common\Collections\ArrayCollection;
	use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

	class UserTest extends KernelTestCase {

		/**
		 * Return an entity
		 * @return User
		 */
		public function getEntity() : User {
			return (new User())
				->setUsername("testPseudo")
				->setRoles(["ROLE_USER"])
				->setPassword("testPassword")
				->setEmail("test@mail.fr")
				;
		}

		/**
		 * Checking whether an entity should have errors or not
		 * @param User $user
		 * @param int $nbErrors
		 */
		public function assertHasErrors(User $user, int $nbErrors = 0) {
			self::bootKernel();
			$error = self::$container->get("validator")->validate($user);

			$this->assertCount($nbErrors, $error);
		}

		/**
		 * Test a valid entity
		 */
		public function testValidEntity() {
			$this->assertHasErrors($this->getEntity(), 0);
		}

		/**
		 * Test an invalid entity with Username's field blank
		 */
		public function testInvalidBlankEntity()
		{
			$user = $this->getEntity()
				->setUsername("");

			$this->assertHasErrors($user, 1);
		}

		/**
		 * Test an invalid entity with email's field invalid
		 */
		public function testInvalidEmailEntity() {
			$user = $this->getEntity()
				->setEmail("test")
			;

			$this->assertHasErrors($user, 1);
		}

		public function testAddAndRemoveUser() {

			$task = new Task();

			$user = $this->getEntity()
				->addTask($task)
			;
			$this->assertInstanceOf(ArrayCollection::class, $user->getTasks());
			$this->assertNotEmpty($user->getTasks());
			$user->removeTask($task);
			$this->assertEmpty($user->getTasks());
		}
	}