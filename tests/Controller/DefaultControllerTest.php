<?php

	namespace App\Tests\Controller;

	use App\Entity\User;
	use App\Tests\LoginUser;
	use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
	use Symfony\Component\HttpFoundation\Response;

	class DefaultControllerTest extends WebTestCase {

		use LoginUser;

		public function testHelloPage() {

			$client = static::createClient();
			$client->request("GET", "/login");

			$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		}

		public function testHelloPageWithAuthenticateUser() {

			$client = static::createClient();

			$userRepo = $client->getContainer()->get("doctrine")->getManager();

			$user = $userRepo->getRepository(User::class)->findOneBy(["username" => "admin"]);

			$this->login($client, $user);

			$client->request("GET", "/");

			$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		}
	}