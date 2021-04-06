<?php

	namespace App\Tests;

	use App\Entity\User;
	use Symfony\Bundle\FrameworkBundle\KernelBrowser;
	use Symfony\Component\BrowserKit\Cookie;
	use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

	trait LoginUser {

		public function login(KernelBrowser $kernelBrowser, User $user) {

			$session = $kernelBrowser->getContainer()->get("session");

			$token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
			$session->set("_security_main", serialize($token));
			$session->save();

			$cookie = new Cookie($session->getName(), $session->getId());
			$kernelBrowser->getCookieJar()->set($cookie);
		}
	}