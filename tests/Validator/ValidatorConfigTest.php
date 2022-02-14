<?php

declare(strict_types=1);

namespace Lamoda\GS1Parser\Tests\Parser;

use Lamoda\GS1Parser\Validator\ValidatorConfig;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lamoda\GS1Parser\Validator\ValidatorConfig
 */
final class ValidatorConfigTest extends TestCase
{
    public function testConfigDefaults(): void
    {
        $config = new ValidatorConfig();

        self::assertEmpty($config->getRequiredAIs());
        self::assertEmpty($config->getForbiddenAIs());
        self::assertEmpty($config->getAIConstraints());
        self::assertFalse($config->isAllowEmpty());
    }

    public function testGettersSetters(): void
    {
        $config = (new ValidatorConfig())
            ->setAllowEmpty(true)
            ->setRequiredAIs(['10'])
            ->setForbiddenAIs(['01'])
            ->setAIConstraints(['01' => fn(string $ai) => true]);

        self::assertTrue($config->isAllowEmpty());
        self::assertEquals(['10'], $config->getRequiredAIs());
        self::assertEquals(['01'], $config->getForbiddenAIs());
        self::assertEquals(['01' => fn(string $ai) => true], $config->getAIConstraints());
    }
}
