<?php

	namespace App\Tests\Controller;

	use App\Entity\User;
	use App\Tests\LoginUser;
	use Symfony\Bundle\FrameworkBundle\Console\Application;
	use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
	use Symfony\Component\Console\Input\StringInput;
	use Symfony\Component\HttpFoundation\Response;

	class DefaultControllerTest extends WebTestCase {

		use LoginUser;

		private $client;

		protected static $application;

		protected function setUp() :void {
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
	}