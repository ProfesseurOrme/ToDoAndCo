<?php

	namespace App\Tests\Form;

	use App\Entity\Task;
	use App\Form\TaskType;
	use Symfony\Component\Form\Test\TypeTestCase;

	/*
	class TaskTypeTest extends TypeTestCase {

		private function formDataTask(): array {
			return [
				'title' => 'Test de titre',
				'content' => 'Test de description',
			];
		}

		public function testSubmitValidData()
		{
			$formData = $this->formDataTask();

			$model = new Task();
			// $formData will retrieve data from the form submission; pass it as the second argument
			$form = $this->factory->create(TaskType::class, $model);

			$expected = (new Task())
				->setTitle($formData['title'])
				->setContent($formData['content'])
			;
			// ...populate $object properties with the data stored in $formData

			// submit the data to the form directly
			$form->submit($formData);

			// This check ensures there are no transformation failures
			$this->assertTrue($form->isSynchronized());

			// check that $formData was modified as expected when the form was submitted
			$this->assertEquals($expected, $model);
		}
	}

	*/