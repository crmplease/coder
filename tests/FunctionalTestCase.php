<?php
declare(strict_types=1);

namespace Tests\CrmPlease\Coder;

use CrmPlease\Coder\Coder;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use function copy;
use function count;
use function explode;
use function file_get_contents;
use function get_class;
use function substr;

/**
 * @author Mougrim <rinat@mougrim.ru>
 */
abstract class FunctionalTestCase extends TestCase
{
    private static $coder;

    protected function getCoder(): Coder
    {
        if (self::$coder === null) {
            self::$coder = Coder::create()
                ->setShowProgressBar(false);
        }
        return self::$coder;
    }

    private function getFixturePaths(string $name): array
    {
        $class = get_class($this);
        $parts = explode('\\', $class);
        $folder = substr($parts[count($parts) - 1], 0, -4);
        return [
            'original' => __DIR__ . "/fixtures/{$folder}/{$name}.php",
            'expected' => __DIR__ . "/fixtures/{$folder}/{$name}Expected.php",
            'result' => __DIR__ . "/fixtures/{$folder}/{$name}Result.php",
        ];
    }

    protected function createFixtureFile(string $name): string
    {
        $paths = $this->getFixturePaths($name);
        copy($paths['original'], $paths['result']);
        return $paths['result'];
    }

    protected function assertFixture($name): void
    {
        $paths = $this->getFixturePaths($name);
        static::assertFileSame(
            $paths['expected'],
            $paths['result']
        );
    }

    /**
     * Asserts that the contents of one file is same to the contents of another
     * file.
     *
     * @param string $expected
     * @param string $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public static function assertFileSame(string $expected, string $actual, string $message = ''): void
    {
        static::assertFileExists($expected, $message);
        static::assertFileExists($actual, $message);

        $constraint = new IsIdentical(file_get_contents($expected));

        static::assertThat(file_get_contents($actual), $constraint, $message);
    }
}
