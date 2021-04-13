<?php

	namespace App\Tests\Controller;

	use App\Tests\LoginUser;
	use Symfony\Component\HttpFoundation\Response;

	class TaskControllerTest extends ExtendedWebTestCase {

		use LoginUser;

		private $client;

		protected function setUp() :void {
			$this->initializeTest();
			self::ensureKernelShutdown();
			$this->client = static::createClient();
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

		protected function tearDown(): void
		{
			$this->tearDownTest();
		}
	}