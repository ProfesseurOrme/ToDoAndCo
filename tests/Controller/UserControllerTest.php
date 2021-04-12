<?php

	namespace App\Tests\Controller;

	use Faker;
	use App\Tests\LoginUser;
	use Symfony\Bundle\FrameworkBundle\Console\Application;
	use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
	use Symfony\Component\Console\Input\StringInput;
	use Symfony\Component\HttpFoundation\Response;

	class UserControllerTest extends WebTestCase {

		use LoginUser;

		private $client;
		private $faker;

		protected static $application;

		protected function setUp() :void {
			$this->faker = Faker\Factory::create();
			self::runCommand("doctrine:database:create --env=test");
			self::runCommand('doctrine:schema:update --force --env=test');
			self::runCommand("doctrine:fixtures:load --env=test -n");
			self::ensureKernelShutdown();
			$this->client = static::createClient();
		}

		protected static function runCommand($command)
		{
			$command = sprintf('%s --quiet', $command);

			return self::getApplication()->run(new StringInput($command));
		}

		protected static function getApplication()
		{
			if (null === self::$application) {

				$kernel = static::createKernel();
				self::$application = new Application($kernel);
				self::$application->setAutoExit(false);
			}

			return self::$application;
		}

		protected function tearDown(): void
		{
			self::runCommand("doctrine:schema:drop --env=test --force");
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

		public function testDisplayFormCreateUser() {
			$this->client->request("GET", "/users/create");

			$this->assertResponseStatusCodeSame(Response::HTTP_OK);
			$this->assertSelectorTextContains("h1", "Créer un utilisateur");
		}

		private function getFormCreateUser(): \Symfony\Component\DomCrawler\Form
		{
			$crawler = $this->client->request("GET", "/users/create");

			return $crawler->selectButton("Ajouter")->form([
				"user[username]" => $this->faker->userName,
				"user[password][first]" => "test123",
				"user[password][second]" => "test123",
				"user[email]" => "test@mail.fr",
				"user[roles]" => "ROLE_USER"
			]);
		}

		public function testSubmitFormCreateUserWithoutCredentials() {

			$form = $this->getFormCreateUser();

			$this->client->submit($form);

			$this->client->followRedirect();
			$this->assertRouteSame("user_list");
			$this->client->followRedirect();
			$this->assertRouteSame("login");
			$this->assertSelectorExists(".alert.alert-success");
		}

		public function testSubmitFormCreateUserWithCredentials() {

			$this->loginWithAdminCredentials($this->client);

			$form = $this->getFormCreateUser();

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
	}