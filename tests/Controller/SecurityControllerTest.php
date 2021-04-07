<?php

	namespace App\Tests\Controller;

	use App\Entity\User;
	use App\Tests\LoginUser;
	use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
	use Symfony\Component\HttpFoundation\Response;

	class SecurityControllerTest extends WebTestCase {

		use LoginUser;

		private $client;

		protected function setUp() :void
		{
			$this->client = static::createClient();
		}

		public function testLoginSuccess() {

			$crawler = $this->client->request("GET", "login");

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

			$crawler = $this->client->request("GET", "/");

			$link = $crawler->selectLink("Se déconnecter")->link();

			$this->client->click($link);

			$this->assertRouteSame("logout");
			$this->client->followRedirect();
			$this->assertRouteSame("homepage");
			$this->client->followRedirect();
			$this->assertRouteSame("login");
			$this->assertResponseStatusCodeSame(Response::HTTP_OK);
		}
	}