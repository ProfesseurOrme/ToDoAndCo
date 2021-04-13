<?php

	namespace App\Tests\Controller;

	use App\Entity\User;
	use App\Tests\LoginUser;
	use Symfony\Component\HttpFoundation\Response;

	class DefaultControllerTest extends ExtendedWebTestCase {

		use LoginUser;

		private $client;

		protected function setUp() :void {
			$this->initializeTest();
			self::ensureKernelShutdown();
			$this->client = static::createClient();
		}

		public function testHelloPage() {
			$this->client->request("GET", "/login");

			$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		}

		public function testHelloPageWithAuthenticateUser() {

			$client = $this->client;

			$userRepo = $client->getContainer()->get("doctrine")->getManager();

			$user = $userRepo->getRepository(User::class)->findOneBy(["username" => "admin"]);

			$this->login($client, $user);

			$client->request("GET", "/");

			$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		}

		protected function tearDown(): void
		{
			$this->tearDownTest();
		}
	}