<?php

	namespace App\Tests\Controller;

	use App\Entity\User;
	use App\Tests\LoginUser;
	use Faker;
	use Symfony\Bundle\FrameworkBundle\Console\Application;
	use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
	use Symfony\Component\Console\Input\StringInput;
	use Symfony\Component\HttpFoundation\Response;

	class SecurityControllerTest extends WebTestCase {

		use LoginUser;

		private $client;
		private $faker;

		protected static $application;

		protected function setUp() :void {
			self::runCommand("doctrine:database:create --env=test");
			self::runCommand('doctrine:schema:update --force --env=test');
			self::runCommand("doctrine:fixtures:load --env=test -n");
			$this->faker = Faker\Factory::create();
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
	}