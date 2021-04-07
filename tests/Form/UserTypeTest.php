<?php

	namespace App\Tests\Form;

	use App\Entity\User;
	use App\Form\UserType;
	use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
	use Symfony\Component\Form\Test\TypeTestCase;
	use Symfony\Component\Validator\Validation;
/*
	class UserTypeTest extends TypeTestCase {

		protected function getExtensions(): array
		{
			$validator = Validation::createValidator();

			return [
				new ValidatorExtension($validator),
			];
		}

		private function formDataUser(): array {
			return [
				"username" => "TestFormType",
				"password" => [
					"first" => "test123",
					"second" => "test123"
				],
				"email" => "test@mail.fr",
				"roles" => ["ROLE_USER"]
			];
		}

		public function testSubmitValidData()
		{
			$formData = $this->formDataUser();

			$model = new User();
			// $formData will retrieve data from the form submission; pass it as the second argument
			$form = $this->factory->create(UserType::class, $model);

			$expected = (new User())
				->setUsername($formData["username"])
				->setPassword($formData["password"]["first"])
				->setEmail($formData["email"])
				->setRoles($formData["roles"])
			;
			// ...populate $object properties with the data stored in $formData

			// submit the data to the form directly
			$form->submit($formData);

			// This check ensures there are no transformation failures
			$this->assertTrue($form->isSynchronized());

			// check that $formData was modified as expected when the form was submitted
			dd($model);
			$this->assertEquals($expected, $model);
		}
	}
*/