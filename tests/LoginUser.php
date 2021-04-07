<?php

	namespace App\Tests;

	use App\Entity\User;
	use Symfony\Bundle\FrameworkBundle\KernelBrowser;
	use Symfony\Component\BrowserKit\Cookie;
	use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

	trait LoginUser {

		private function login(KernelBrowser $kernelBrowser, User $user) {

			$session = $kernelBrowser->getContainer()->get("session");

			$token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
			$session->set("_security_main", serialize($token));
			$session->save();

			$cookie = new Cookie($session->getName(), $session->getId());
			$kernelBrowser->getCookieJar()->set($cookie);
		}

		private function searchUser ($role) {
			$userManager = $this->client->getContainer()->get("doctrine")->getManager();

			return $userManager->getRepository(User::class)->findOneBy(["username" => $role]);
		}

		public function loginWithAdminCredentials(KernelBrowser $client) {
			$this->login($client, $this->searchUser("admin"));
		}

		public function loginWithUserCredentials(KernelBrowser $client) {
			$this->login($client, $this->searchUser("user"));
		}

		public function loginWithAnotherUserCredentials(KernelBrowser $client) {
			$this->login($client, $this->searchUser("user2"));
		}
	}