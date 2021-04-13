<?php

	namespace App\Tests\Controller;

	use App\Tests\LoginUser;
	use Faker;
	use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
	use Symfony\Component\HttpFoundation\Response;

	class SecurityControllerTest extends ExtendedWebTestCase {

		use LoginUser;

		private $client;
		private $faker;

		protected function setUp() :void {
			$this->initializeTest();
			$this->faker = Faker\Factory::create();
			self::ensureKernelShutdown();
			$this->client = static::createClient();
		}

		public function testLoginSuccess() {

			$crawler = $this->client->request("GET", "/login");

			$form = $crawler->selectButton("Se connecter")->form([
				"_username" => "admin",
				"_password" => "test123"
			]);

			$this->client->submit($form);

			$this->client->followRedirect();
			$this->assertRouteSame("homepage");
			$this->assertSelectorTextContains("h1", "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");
		}

		public function testLoginFailure() {

			$crawler = $this->client->request("GET", "/login");

			$form = $crawler->selectButton("Se connecter")->form([
				"_username" => "admin",
				"_password" => "testMauvaisMotDePasse"
			]);

			$this->client->submit($form);

			$this->client->followRedirect();
			$this->assertRouteSame("login");
			$this->assertSelectorExists(".alert.alert-danger");
		}

		public function testLogout() {

			$this->loginWithAdminCredentials($this->client);

			$this->client->request("GET", "/logout");

			$this->assertRouteSame("logout");
			$this->client->followRedirect();
			$this->assertRouteSame("homepage");
			$this->client->followRedirect();
			$this->assertRouteSame("login");
			$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		}

		protected function tearDown(): void
		{
			$this->tearDownTest();
		}
	}