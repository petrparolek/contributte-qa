<?php declare(strict_types = 1);

namespace Tests\Cases;

use Contributte\Tester\TestCase\BaseTestCase;
use Generator;
use Symfony\Component\Process\Process;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class CodestyleTest extends BaseTestCase
{

	/**
	 * @dataProvider provideGoodSets
	 */
	public function testGoodSets(string $folder): void
	{
		$args = ['vendor/bin/phpcs', '--standard=' . $folder . '/codesniffer.xml', $folder, '--report=json', '--no-colors', '-q'];
		$process = new Process($args);
		$process->setWorkingDirectory(__DIR__ . '/../../');
		$process->run();

		$output = json_decode($process->getOutput());

		$errors = [];
		foreach ($output->files ?? [] as $file) {
			foreach ($file->messages ?? [] as $message) {
				$errors[] = $message->message;
			}
		}

		if (count($errors) > 0) {
			Assert::fail(implode("\n", $errors), $process->getOutput());
		} else {
			Assert::true($process->isSuccessful(), $process->getOutput());
		}
	}

	/**
	 * @dataProvider provideBadSets
	 */
	public function testBadSets(string $folder, string $snapshot): void
	{
		$args = ['vendor/bin/phpcs', '--standard=' . $folder . '/codesniffer.xml', $folder, '--report=checkstyle', '--no-colors', '-q'];
		$process = new Process($args);
		$process->setWorkingDirectory(__DIR__ . '/../../');
		$process->run();

		Assert::false($process->isSuccessful(), $process->getOutput());
		Assert::matchFile($snapshot, trim($process->getOutput()), $process->getOutput());
	}

	public function provideGoodSets(): Generator
	{
		yield [__DIR__ . '/../Sets/base/1-good'];
		yield [__DIR__ . '/../Sets/gamee/1-good'];
		yield [__DIR__ . '/../Sets/next/1-good'];
		yield [__DIR__ . '/../Sets/php80/1-good'];
		yield [__DIR__ . '/../Sets/php81/1-good'];
	}

	public function provideBadSets(): iterable
	{
		yield [__DIR__ . '/../Sets/base/2-bad', __DIR__ . '/../Sets/base/2-bad/snapshot.txt'];
	}

}

(new CodestyleTest())->run();
