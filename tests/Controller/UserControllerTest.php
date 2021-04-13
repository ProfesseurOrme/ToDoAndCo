<?php

	namespace App\Tests\Controller;

	use Faker;
	use App\Tests\LoginUser;
	use Symfony\Component\HttpFoundation\Response;

	class UserControllerTest extends ExtendedWebTestCase {

		use LoginUser;

		private $client;
		private $faker;

		protected function setUp() :void {
			$this->initializeTest();
			$this->faker = Faker\Factory::create();
			self::ensureKernelShutdown();
			$this->client = static::createClient();
		}

		public function testListUserWithAdminCredentials() {

			$this->loginWithAdminCredentials($this->client);
			$this->client->request("GET", "/users");

			$this->assertResponseStatusCodeSame(Response::HTTP_OK);
			$this->assertSelectorTextContains("h1", "Liste des utilisateurs");
		}

		public function testListUserWithoutCredentials() {
			$this->client->request("GET", "/users");

			$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
			$this->client->followRedirect();
			$this->assertRouteSame("login");
		}

		public function testDisplayFormCreateUserWithAnonymousUser() {
			$this->client->request("GET", "/users/create");

			$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
			$this->client->followRedirect();
			$this->assertRouteSame("login");
		}

		public function testDisplayFormCreateUserWithUserRole() {
			$this->loginWithUserCredentials($this->client);
			$this->client->request("GET", "/users/create");

			$this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
		}

		public function testDisplayFormCreateUserWithAdminRole() {
			$this->loginWithAdminCredentials($this->client);
			$this->client->request("GET", "/users/create");

			$this->assertResponseStatusCodeSame(Response::HTTP_OK);
			$this->assertSelectorTextContains("h1", "Créer un utilisateur");
		}

		public function testSubmitFormCreateUserWithoutCredentials() {

			$this->client->request("GET", "/users/create");

			$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
			$this->client->followRedirect();
			$this->assertRouteSame("login");
		}

		public function testSubmitFormCreateUserWithCredentials() {

			$this->loginWithAdminCredentials($this->client);

			$crawler = $this->client->request("GET", "/users/create");

			$form = $crawler->selectButton("Ajouter")->form([
				"user[username]" => $this->faker->userName,
				"user[password][first]" => "test123",
				"user[password][second]" => "test123",
				"user[email]" => "test@mail.fr",
				"user[roles]" => "ROLE_USER"
			]);

			$this->client->submit($form);

			$this->client->followRedirect();
			$this->assertRouteSame("user_list");
			$this->assertSelectorExists(".alert.alert-success");
		}

		public function testFormEditUser() {

			$this->loginWithAdminCredentials($this->client);

			$manager = $this->client->getContainer()->get("doctrine")->getManager();
			$user = $manager->getRepository('App:User')->findOneBy([], ['id' => 'desc']);

			$crawler = $this->client->request("GET", "/users/".$user->getId()."/edit");

			$this->assertRouteSame("user_edit");

			$form = $crawler->selectButton("Modifier")->form([
				"user[password][first]" => "test123",
				"user[password][second]" => "test123",
				"user[email]" => "UPDATEDMAIL@mail.fr",
				"user[roles]" => "ROLE_ADMIN"
			]);

			$this->client->submit($form);

			$this->client->followRedirect();

			$this->assertRouteSame("user_list");
			$this->assertSelectorExists(".alert.alert-success");
		}

		protected function tearDown(): void
		{
			$this->initializeTest();
		}
	}