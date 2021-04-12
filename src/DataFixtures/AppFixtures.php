<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{
	private $passwordEncoder;

	public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
		$this->passwordEncoder = $passwordEncoder;
	}

	public function load(ObjectManager $manager)
	{
			$users = ["admin", "user", "user2"];

			foreach ($users as $value) {
				$user = (new User())
					->setUsername($value)
					->setEmail("test@mail.com")
				;

				if ($value === "admin") {
					$user->setRoles(["ROLE_ADMIN"]);
				} else {
					$user->setRoles(["ROLE_USER"]);
				}
				$encodedPassword = $this->passwordEncoder->encodePassword($user, "test123");
				$user->setPassword($encodedPassword);

				$manager->persist($user);

				for ($count = 0; $count < 3; $count++) {
					$task = (new Task())
						->setTitle("This is a title")
						->setContent("This is a content")
						->setCreatedAt(new \DateTime())
						->setIsDone(false)
						->setUser($user)
					;

					$manager->persist($task);
				}
			}
			$manager->flush();
	}
}
