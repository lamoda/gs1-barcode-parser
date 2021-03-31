<?php

declare(strict_types=1);

namespace Lamoda\GS1Parser\Tests\Parser;

use Lamoda\GS1Parser\Barcode;
use Lamoda\GS1Parser\Exception\InvalidBarcodeException;
use Lamoda\GS1Parser\Parser\Parser;
use Lamoda\GS1Parser\Parser\ParserConfig;
use Lamoda\GS1Parser\Tests\BarcodeAssert;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lamoda\GS1Parser\Parser\Parser
 */
final class ParserTest extends TestCase
{
    use BarcodeAssert;

    /**
     * @dataProvider dataParsingValidCode
     */
    public function testParsingValidCode(ParserConfig $config, string $data, Barcode $expected): void
    {
        $parser = new Parser($config);

        $result = $parser->parse($data);

        self::assertBarcodesAreEqual($expected, $result);
    }

    public function dataParsingValidCode(): array
    {
        $defaultConfig = new ParserConfig();

        $defaultConfigWithKnownAIs = (new ParserConfig())
            ->setKnownAIs(['01', '17', '10']);

        $configWithNotRequiredFNC1 = (new ParserConfig())
            ->setFnc1SequenceRequired(false);

        $configForMarkingCode = (new ParserConfig())
            ->setFnc1SequenceRequired(false)
            ->setKnownAIs([
                '01',
                '21',
                '240',
                '91',
                '92'
            ]);

        $anotherSeparator = (new ParserConfig())
            ->setGroupSeparator('|');

        $multiCharSeparator = (new ParserConfig())
            ->setGroupSeparator('<GS>');

        return [
            'base' => [
                $defaultConfig,
                ']d201034531200000111719112510ABCD1234',
                new Barcode(']d201034531200000111719112510ABCD1234', Barcode::TYPE_GS1_DATAMATRIX, [], [
                    '0103453120000011',
                    '17191125',
                    '10ABCD1234',
                ], ']d2')
            ],
            'base - with known ais' => [
                $defaultConfigWithKnownAIs,
                ']d201034531200000111719112510ABCD1234',
                new Barcode(']d201034531200000111719112510ABCD1234', Barcode::TYPE_GS1_DATAMATRIX, [
                    '01' => '03453120000011',
                    '17' => '191125',
                    '10' => 'ABCD1234',
                ], [], ']d2')
            ],
            'base - very short' => [
                $defaultConfig,
                ']d21',
                new Barcode(']d21', Barcode::TYPE_GS1_DATAMATRIX, [], [
                    '1',
                ], ']d2')
            ],
            'base - not req FNC1' => [
                $configWithNotRequiredFNC1,
                ']d201034531200000111719112510ABCD1234',
                new Barcode(']d201034531200000111719112510ABCD1234', Barcode::TYPE_GS1_DATAMATRIX, [], [
                    '0103453120000011',
                    '17191125',
                    '10ABCD1234'
                ], ']d2')
            ],
            'base - not req FNC1, FNC1 not present' => [
                $configWithNotRequiredFNC1,
                '01034531200000111719112510ABCD1234',
                new Barcode('01034531200000111719112510ABCD1234', Barcode::TYPE_UNKNOWN, [], [
                    '0103453120000011',
                    '17191125',
                    '10ABCD1234'
                ], '')
            ],
            'switched position #1' => [
                $defaultConfig,
                ']d217191125010345312000001110ABCD1234',
                new Barcode(']d217191125010345312000001110ABCD1234', Barcode::TYPE_GS1_DATAMATRIX, [], [
                    '17191125',
                    '0103453120000011',
                    '10ABCD1234'
                ], ']d2')
            ],
            'switched position #2' => [
                $defaultConfig,
                "]d210ABCD1234\u{001d}171911250103453120000011",
                new Barcode("]d210ABCD1234\u{001d}171911250103453120000011", Barcode::TYPE_GS1_DATAMATRIX, [], [
                    '10ABCD1234',
                    '17191125',
                    '0103453120000011',
                ], ']d2')
            ],
            'another separator' => [
                $anotherSeparator,
                ']d2010345312000001110ABCD1234|17191125',
                new Barcode(']d2010345312000001110ABCD1234|17191125', Barcode::TYPE_GS1_DATAMATRIX, [], [
                    '0103453120000011',
                    '10ABCD1234',
                    '17191125',
                ], ']d2')
            ],
            'multi char separator' => [
                $multiCharSeparator,
                ']d2010345312000001110ABCD1234<GS>17191125',
                new Barcode(']d2010345312000001110ABCD1234<GS>17191125', Barcode::TYPE_GS1_DATAMATRIX, [], [
                    '0103453120000011',
                    '10ABCD1234',
                    '17191125',
                ], ']d2')
            ],
            'russian product marking code' => [
                $configWithNotRequiredFNC1,
                "010467003301005321gJk6o54AQBJfX\u{001d}2406401\u{001d}91ffd0\u{001d}92LGYcm3FRQrRdNOO+8t0pz78QTyxxBmYKhLXaAS03jKV7oy+DWGy1SeU+BZ8o7B8+hs9LvPdNA7B6NPGjrCm34A==",
                new Barcode("010467003301005321gJk6o54AQBJfX\u{001d}2406401\u{001d}91ffd0\u{001d}92LGYcm3FRQrRdNOO+8t0pz78QTyxxBmYKhLXaAS03jKV7oy+DWGy1SeU+BZ8o7B8+hs9LvPdNA7B6NPGjrCm34A==", Barcode::TYPE_UNKNOWN, [], [
                    '0104670033010053',
                    '21gJk6o54AQBJfX',
                    '2406401',
                    '91ffd0',
                    '92LGYcm3FRQrRdNOO+8t0pz78QTyxxBmYKhLXaAS03jKV7oy+DWGy1SeU+BZ8o7B8+hs9LvPdNA7B6NPGjrCm34A=='
                ], '')
            ],
            'russian product marking code - known ais' => [
                $configForMarkingCode,
                "010467003301005321gJk6o54AQBJfX\u{001d}2406401\u{001d}91ffd0\u{001d}92LGYcm3FRQrRdNOO+8t0pz78QTyxxBmYKhLXaAS03jKV7oy+DWGy1SeU+BZ8o7B8+hs9LvPdNA7B6NPGjrCm34A==",
                new Barcode("010467003301005321gJk6o54AQBJfX\u{001d}2406401\u{001d}91ffd0\u{001d}92LGYcm3FRQrRdNOO+8t0pz78QTyxxBmYKhLXaAS03jKV7oy+DWGy1SeU+BZ8o7B8+hs9LvPdNA7B6NPGjrCm34A==", Barcode::TYPE_UNKNOWN, [
                    '01' => '04670033010053',
                    '21' => 'gJk6o54AQBJfX',
                    '240' => '6401',
                    '91' => 'ffd0',
                    '92' => 'LGYcm3FRQrRdNOO+8t0pz78QTyxxBmYKhLXaAS03jKV7oy+DWGy1SeU+BZ8o7B8+hs9LvPdNA7B6NPGjrCm34A=='
                ], [], '')
            ],
            'russian product marking code - known ais, no tnved' => [
                $configForMarkingCode,
                "010467003301005321gJk6o54AQBJfX\u{001d}91ffd0\u{001d}92LGYcm3FRQrRdNOO+8t0pz78QTyxxBmYKhLXaAS03jKV7oy+DWGy1SeU+BZ8o7B8+hs9LvPdNA7B6NPGjrCm34A==",
                new Barcode("010467003301005321gJk6o54AQBJfX\u{001d}91ffd0\u{001d}92LGYcm3FRQrRdNOO+8t0pz78QTyxxBmYKhLXaAS03jKV7oy+DWGy1SeU+BZ8o7B8+hs9LvPdNA7B6NPGjrCm34A==", Barcode::TYPE_UNKNOWN, [
                    '01' => '04670033010053',
                    '21' => 'gJk6o54AQBJfX',
                    '91' => 'ffd0',
                    '92' => 'LGYcm3FRQrRdNOO+8t0pz78QTyxxBmYKhLXaAS03jKV7oy+DWGy1SeU+BZ8o7B8+hs9LvPdNA7B6NPGjrCm34A=='
                ], [], '')
            ],
        ];
    }

    /**
     * @dataProvider dataParsingInvalidBarcode
     */
    public function testParsingInvalidBarcode(
        ParserConfig $config,
        string $data,
        string $expectedExceptionMessage
    ): void {
        $parser = new Parser($config);

        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $parser->parse($data);
    }

    /**
     * @return array
     */
    public function dataParsingInvalidBarcode(): array
    {
        $default = new ParserConfig();

        return [
            'empty' => [
                $default,
                '',
                'Barcode is empty',
            ],
            'no fnc1' => [
                $default,
                '01034531200000111719112510ABCD1234',
                'FNC1 sequence is not found at the start of barcode',
            ],
            'no data after fnc1' => [
                $default,
                ']d2',
                'Barcode does not contain data',
            ],
            'invalid data for fixed length ai' => [
                $default,
                ']d2010345',
                'Not enough data for AI "01": 16 expected but 6 exists',
            ],
            'group separator inside fixed length data' => [
                $default,
                "]d20103453\u{001d}200000111719112510ABCD1234",
                'Group separator was not expected in AI "010345320000011"'
            ],
            'value contains invalid characters' => [
                $default,
                "]d2010 3`5'1200000111719112510ABCD123",
                'Value contains invalid characters: " ", "`"'
            ],
        ];
    }
}
