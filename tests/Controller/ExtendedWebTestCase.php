<?php

	namespace App\Tests\Controller;

	use Symfony\Bundle\FrameworkBundle\Console\Application;
	use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
	use Symfony\Component\Console\Input\StringInput;

/**
 * Class ExtendedWebTestCase
 * @package App\Tests
 *
 * This class extends the WebTestCase class which allows me to test my Controllers
 *
 * It adds the fact of being able to create and purge a test database in order to be able to verify use cases in tests.
 *
 * For that, don't forget to create Fixtures and and a .env.test.local file in project root folder with Database URL
 */
class ExtendedWebTestCase extends WebTestCase
{
	private static $application;

	public function initializeTest() : void {
		self::runCommand("doctrine:database:create --env=test");
		self::runCommand("doctrine:schema:update --force --env=test");
		self::runCommand("doctrine:fixtures:load --env=test -n");
	}

	public function tearDownTest() :void {
		self::runCommand("doctrine:schema:drop --env=test --force");
	}

	private function runCommand($command): int
	{
		$command = sprintf('%s --quiet', $command);

		return self::getApplication()->run(new StringInput($command));
	}

	protected static function getApplication(): Application
	{
		if (null === self::$application) {
			$kernel = static::createKernel();
			self::$application = new Application($kernel);
			self::$application->setAutoExit(false);
		}
		return self::$application;
	}
}