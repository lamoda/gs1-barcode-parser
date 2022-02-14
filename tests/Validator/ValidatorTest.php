<?php

declare(strict_types=1);

namespace Lamoda\GS1Parser\Tests\Validator;

use Lamoda\GS1Parser\Barcode;
use Lamoda\GS1Parser\Exception\InvalidBarcodeException;
use Lamoda\GS1Parser\Parser\ParserInterface;
use Lamoda\GS1Parser\Validator\ErrorCodes;
use Lamoda\GS1Parser\Validator\Resolution;
use Lamoda\GS1Parser\Validator\Validator;
use Lamoda\GS1Parser\Validator\ValidatorConfig;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lamoda\GS1Parser\Validator\Validator
 */
final class ValidatorTest extends TestCase
{
    /** @var ParserInterface | MockObject */
    private $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = $this->createMock(ParserInterface::class);
    }

    /**
     * @dataProvider dataValidate
     */
    public function testValidate(ValidatorConfig $config, $value, Resolution $expected): void
    {
        $barcode = new Barcode(']d201034531200000111719112510ABCD1234', Barcode::TYPE_GS1_DATAMATRIX, [
            '01' => '03453120000011',
        ], [
            '17191125',
            '10ABCD1234',
        ], ']d2');

        $this->parser->method('parse')
            ->with($value)
            ->willReturn($barcode);

        $validator = new Validator($this->parser, $config);

        $result = $validator->validate($value);

        $this->assertEquals($expected->isValid(), $result->isValid());
        $this->assertEquals(array_keys($expected->getErrors()), array_keys($result->getErrors()));
    }

    public function dataValidate(): array
    {
        $default = new ValidatorConfig();
        $allowEmpty = (new ValidatorConfig())
            ->setAllowEmpty(true);
        $requiredAI = (new ValidatorConfig())
            ->setRequiredAIs(['10']);
        $forbiddenAI = (new ValidatorConfig())
            ->setForbiddenAIs(['01']);
        $aiConstraintReturnTrue = (new ValidatorConfig())
            ->setAIConstraints(['01' => fn (string $ai) => true]);
        $aiConstraintReturnFalse = (new ValidatorConfig())
            ->setAIConstraints(['01' => fn (string $ai) => false]);

        return [
            'valid' => [
                $default,
                ']d201034531200000111719112510ABCD1234',
                Resolution::createValid(),
            ],
            'valid - allow empty' => [
                $allowEmpty,
                '',
                Resolution::createValid(),
            ],
            'valid - allow empty - null' => [
                $allowEmpty,
                null,
                Resolution::createValid(),
            ],
            'not a string - null' => [
                $default,
                null,
                Resolution::createInvalid([
                    ErrorCodes::VALUE_IS_NOT_STRING => '',
                ]),
            ],
            'not a string - number' => [
                $default,
                10,
                Resolution::createInvalid([
                    ErrorCodes::VALUE_IS_NOT_STRING => '',
                ]),
            ],
            'empty' => [
                $default,
                '',
                Resolution::createInvalid([
                    ErrorCodes::VALUE_EMPTY => '',
                ]),
            ],
            'missing required ai' => [
                $requiredAI,
                ']d201034531200000111719112512ABCD1234',
                Resolution::createInvalid([
                    ErrorCodes::MISSING_AIS => '',
                ]),
            ],
            'forbidden ai' => [
                $forbiddenAI,
                ']d201034531200000111719112511ABCD1234',
                Resolution::createInvalid([
                    ErrorCodes::FORBIDDEN_AIS => '',
                ]),
            ],
            'ai constraint return true' => [
                $aiConstraintReturnTrue,
                ']d201034531200000111719112511ABCD1234',
                Resolution::createValid(),
            ],
            'ai constraint return false' => [
                $aiConstraintReturnFalse,
                ']d201034531200000111719112511ABCD1234',
                Resolution::createInvalid([
                    ErrorCodes::INVALID_VALUE => 'AI is invalid: code=01, value=03453120000011',
                ]),
            ],
        ];
    }

    /**
     * @dataProvider dataValidate
     */
    public function testValidateOnParsingError(): void
    {
        $config = new ValidatorConfig();
        $value = ']d201034531200000111719112510ABCD1234';

        $this->parser->method('parse')
            ->with($value)
            ->willThrowException(new InvalidBarcodeException('test'));

        $validator = new Validator($this->parser, $config);

        $result = $validator->validate($value);

        $this->assertFalse($result->isValid());
        $this->assertEquals([ErrorCodes::INVALID_VALUE], array_keys($result->getErrors()));
    }
}
