<?php

	namespace App\Tests\Controller;

	use App\Tests\LoginUser;
	use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
	use Symfony\Bundle\FrameworkBundle\Console\Application;
	use Symfony\Component\Console\Input\StringInput;
	use Symfony\Component\HttpFoundation\Response;

	class TaskControllerTest extends WebTestCase {

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

		public function testListTasks() {
			$this->loginWithAdminCredentials($this->client);

			$crawler = $this->client->request("GET", "/tasks");

			$this->assertResponseStatusCodeSame(Response::HTTP_OK);
			$this->assertRouteSame("task_list");
		}

		public function testListTasksWithoutCredentials() {
			$crawler = $this->client->request("GET", "/tasks");

			$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
			$this->client->followRedirect();
			$this->assertRouteSame("login");
		}

		public function testCreateTask() {

			$this->loginWithUserCredentials($this->client);

			$crawler = $this->client->request("GET", "/tasks/create");

			$form = $crawler->selectButton("Ajouter")->form([
				"task[title]" => "Test de titre",
				"task[content]" => "Test de contenu super génial"
			]);

			$this->client->submit($form);

			$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
			$this->client->followRedirect();
			$this->assertRouteSame("task_list");
			$this->assertSelectorExists(".alert.alert-success");
		}

		public function testEditTask() {
			$this->loginWithUserCredentials($this->client);

			$crawler = $this->client->request("GET", "/tasks/4/edit");

			$form = $crawler->selectButton("Modifier")->form([
				"task[title]" => "Test de titre",
				"task[content]" => "Test de contenu pas trop génial"
			]);

			$this->client->submit($form);

			$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
			$this->client->followRedirect();
			$this->assertRouteSame("task_list");
			$this->assertSelectorExists(".alert.alert-success");
		}

		public function testEditTaskWithAnotherUserCredentials() {
			$this->loginWithAnotherUserCredentials($this->client);

			$crawler = $this->client->request("GET", "/tasks/4/edit");

			$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
			$this->client->followRedirect();
			$this->assertRouteSame("task_list");
		}

		public function testEditTaskWithAdminCredentials() {
			$this->loginWithAdminCredentials($this->client);

			$crawler = $this->client->request("GET", "/tasks/4/edit");

			$form = $crawler->selectButton("Modifier")->form([
				"task[title]" => "Test de titre",
				"task[content]" => "Test de contenu pas trop génial selon l'Admin"
			]);

			$this->client->submit($form);

			$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
			$this->client->followRedirect();
			$this->assertRouteSame("task_list");
			$this->assertSelectorExists(".alert.alert-success");
		}

		private function getToggleTask() : void {
			$this->loginWithUserCredentials($this->client);

			$this->client->request("GET", "/tasks/4/toggle");

			$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
			$this->client->followRedirect();
			$this->assertRouteSame("task_list");
		}

		public function testToggleTaskToTrue() {
			$this->getToggleTask();
			$this->assertSelectorExists(".alert.alert-success");
		}

		public function testToggleTaskToFalse() {
			$this->getToggleTask();
			$this->assertSelectorExists(".alert.alert-success");
			$this->getToggleTask();
			$this->assertSelectorExists(".alert.alert-warning");
		}

		public function testDeleteTaskWithAnotherUser() {
			$this->loginWithAnotherUserCredentials($this->client);
			$crawler = $this->client->request("GET", "/tasks/4/delete");

			$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
			$this->client->followRedirect();
			$this->assertRouteSame("task_list");
		}

		public function testDeleteTaskWithAdminCredentials() {
			$this->loginWithAdminCredentials($this->client);

			$crawler = $this->client->request("GET", "/tasks/4/delete");

			$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
			$this->client->followRedirect();
			$this->assertRouteSame("task_list");
			$this->assertSelectorExists(".alert.alert-success");
		}
	}