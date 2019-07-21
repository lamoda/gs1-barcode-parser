<?php

declare(strict_types=1);

namespace Lamoda\GS1Parser\Tests\Parser;

use Lamoda\GS1Parser\Barcode;
use Lamoda\GS1Parser\Constants;
use Lamoda\GS1Parser\Parser\ParserConfig;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lamoda\GS1Parser\Parser\ParserConfig
 */
final class ParserConfigTest extends TestCase
{
    public function testConfigDefaults(): void
    {
        $config = new ParserConfig();

        $this->assertNotEmpty($config->getGroupSeparator());
        $this->assertNotEmpty($config->getFnc1PrefixMap());
        $this->assertEmpty($config->getKnownAIs());
        $this->assertTrue($config->isFnc1SequenceRequired());
    }

    public function testGettersSetters(): void
    {
        $config = (new ParserConfig())
            ->setKnownAIs(['10'])
            ->setFnc1SequenceRequired(false)
            ->setFnc1PrefixMap([
                Constants::FNC1_GS1_EAN_SEQUENCE => Barcode::TYPE_EAN,
            ])
            ->setGroupSeparator(Constants::GROUP_SEPARATOR_SYMBOL);

        $this->assertEquals(['10'], $config->getKnownAIs());
        $this->assertEquals([
            Constants::FNC1_GS1_EAN_SEQUENCE => Barcode::TYPE_EAN,
        ], $config->getFnc1PrefixMap());
        $this->assertFalse($config->isFnc1SequenceRequired());
        $this->assertEquals(Constants::GROUP_SEPARATOR_SYMBOL, $config->getGroupSeparator());
    }
}
