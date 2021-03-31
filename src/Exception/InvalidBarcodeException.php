<?php

declare(strict_types=1);

namespace Lamoda\GS1Parser\Exception;

use InvalidArgumentException;

final class InvalidBarcodeException extends InvalidArgumentException implements GS1ParserExceptionInterface
{
    public static function becauseBarcodeIsEmpty(): self
    {
        return new static('Barcode is empty');
    }

    public static function becauseFNC1SequenceIsNotFound(): self
    {
        return new static('FNC1 sequence is not found at the start of barcode');
    }

    public static function becauseNoDataPresent(): self
    {
        return new static('Barcode does not contain data');
    }

    public static function becauseNotEnoughDataFoAI(string $ai, int $expectedLength, int $actualLength): self
    {
        return new static(sprintf(
            'Not enough data for AI "%s": %d expected but %d exists',
            $ai,
            $expectedLength,
            $actualLength
        ));
    }

    public static function becauseGroupSeparatorWasNotExpected(string $value): self
    {
        return new static(sprintf('Group separator was not expected in AI "%s"', $value));
    }

    public static function becauseValueContainsInvalidCharacters(string ...$invalidCharacters): self
    {
        return new static(sprintf(
            'Value contains invalid characters: %s',
            implode(', ', array_map(static function (string $character) {
                return "\"$character\"";
            }, $invalidCharacters))
        ));
    }
}
