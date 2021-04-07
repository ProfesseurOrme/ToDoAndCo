<?php

	namespace App\Tests\Controller;

	use App\Tests\LoginUser;
	use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
	use Symfony\Component\HttpFoundation\Response;

	class TaskControllerTest extends WebTestCase {

		use LoginUser;

		private $client;

		protected function setUp() :void {
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

		/**
		 * Return the last task created with the previous test
		 */
		private function getLastTaskForTest() {
			$manager = $this->client->getContainer()->get("doctrine")->getManager();
			return $manager->getRepository('App:Task')->findOneBy([], ['id' => 'desc']);
		}

		public function testEditTask() {
			$this->loginWithUserCredentials($this->client);

			$task = $this->getLastTaskForTest();

			$crawler = $this->client->request("GET", "/tasks/".$task->getId()."/edit");

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

			$task = $this->getLastTaskForTest();

			$crawler = $this->client->request("GET", "/tasks/".$task->getId()."/edit");

			$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
			$this->client->followRedirect();
			$this->assertRouteSame("task_list");
		}

		public function testEditTaskWithAdminCredentials() {
			$this->loginWithAdminCredentials($this->client);

			$task = $this->getLastTaskForTest();

			$crawler = $this->client->request("GET", "/tasks/".$task->getId()."/edit");

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

		public function testToggleTask() {
			$this->loginWithUserCredentials($this->client);

			$task = $this->getLastTaskForTest();

			$this->client->request("GET", "/tasks/".$task->getId()."/toggle");

			$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
			$this->client->followRedirect();
			$this->assertRouteSame("task_list");
			if($task->getIsDone()) {
				$this->assertSelectorExists(".alert.alert-success");
			}else {
				$this->assertSelectorExists(".alert.alert-warning");
			}
		}

		public function testDeleteTaskWithAnotherUser() {
			$this->loginWithAnotherUserCredentials($this->client);

			$task = $this->getLastTaskForTest();

			$crawler = $this->client->request("GET", "/tasks/".$task->getId()."/delete");

			$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
			$this->client->followRedirect();
			$this->assertRouteSame("task_list");
		}

		public function testDeleteTaskWithAdminCredentials() {
			$this->loginWithAdminCredentials($this->client);

			$task = $this->getLastTaskForTest();

			$crawler = $this->client->request("GET", "/tasks/".$task->getId()."/delete");

			$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
			$this->client->followRedirect();
			$this->assertRouteSame("task_list");
			$this->assertSelectorExists(".alert.alert-success");
		}
	}