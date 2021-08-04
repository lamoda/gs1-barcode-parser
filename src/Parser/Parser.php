<?php

declare(strict_types=1);

namespace Lamoda\GS1Parser\Parser;

use Lamoda\GS1Parser\Barcode;
use Lamoda\GS1Parser\Exception\InvalidBarcodeException;

/**
 * Performs barcode parsing according to
 * https://www.gs1.org/sites/default/files/docs/barcodes/GS1_General_Specifications.pdf
 */
final class Parser implements ParserInterface
{
    private const ENCODABLE_VALUE_CHARACTERS_SET = [
        '!',
        '"',
        '%',
        '&',
        '\'',
        '(',
        ')',
        '*',
        '+',
        ',',
        '-',
        '_',
        '.',
        '/',
        '0',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        ':',
        ';',
        '<',
        '=',
        '>',
        '?',
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
        'a',
        'b',
        'c',
        'd',
        'e',
        'f',
        'g',
        'h',
        'i',
        'j',
        'k',
        'l',
        'm',
        'n',
        'o',
        'p',
        'q',
        'r',
        's',
        't',
        'u',
        'v',
        'w',
        'x',
        'y',
        'z',
    ];
    private const FIXED_LENGTH_AIS = [
        '00' => 20,
        '01' => 16,
        '02' => 16,
        '03' => 16,
        '04' => 18,
        '11' => 8,
        '12' => 8,
        '13' => 8,
        '14' => 8,
        '15' => 8,
        '16' => 8,
        '17' => 8,
        '18' => 8,
        '19' => 8,
        '20' => 4,
        '31' => 10,
        '32' => 10,
        '33' => 10,
        '34' => 10,
        '35' => 10,
        '36' => 10,
        '41' => 16,
    ];
    private const FIXED_AI_LENGTH = 2;

    /**
     * @var ParserConfig
     */
    private $config;

    public function __construct(ParserConfig $config)
    {
        $this->config = $config;
    }

    public function parse(string $data): Barcode
    {
        $data = trim($data);

        if ($data === '') {
            throw InvalidBarcodeException::becauseBarcodeIsEmpty();
        }

        [$fnc1Prefix, $codeType] = $this->fetchFNC1Prefix($data);

        if ($fnc1Prefix === null && $this->config->isFnc1SequenceRequired()) {
            throw InvalidBarcodeException::becauseFNC1SequenceIsNotFound();
        }

        $codeOffset = strlen((string)$fnc1Prefix);
        $dataLength = strlen($data);

        if ($dataLength <= $codeOffset) {
            throw InvalidBarcodeException::becauseNoDataPresent();
        }

        $position = $codeOffset;
        $foundAIs = [];
        $buffer = [];
        while ($position < $dataLength) {
            [$ai, $length] = $this->fetchFixedAI($data, $position, $dataLength);
            $value = null;

            if ($ai !== null) {
                if ($position + $length > $dataLength) {
                    throw InvalidBarcodeException::becauseNotEnoughDataFoAI(
                        $ai,
                        $length,
                        $dataLength - $position
                    );
                }

                $isKnownAI = in_array($ai, $this->config->getKnownAIs(), true);

                if ($isKnownAI) {
                    $value = substr($data, $position + self::FIXED_AI_LENGTH, $length - self::FIXED_AI_LENGTH);
                } else {
                    $ai = null;
                    $value = substr($data, $position, $length);
                }

                if (strpos($value, $this->config->getGroupSeparator()) !== false) {
                    throw InvalidBarcodeException::becauseGroupSeparatorWasNotExpected($value);
                }

                $position += $length;
            } else {
                [$ai, $aiLength] = $this->fetchKnownAI($data, $position);

                $groupSeparatorPosition = strpos($data, $this->config->getGroupSeparator(), $position);
                if ($groupSeparatorPosition !== false) {
                    $length = $groupSeparatorPosition - $position;
                } else {
                    $length = $dataLength - $position;
                }

                if ($ai) {
                    $value = substr($data, $position + $aiLength, $length - $aiLength);
                } else {
                    $value = substr($data, $position, $length);
                }

                $position += $length + strlen($this->config->getGroupSeparator());
            }

            if ($length > 0) {
                $this->assertValueIsValid($value);

                if ($ai) {
                    $foundAIs[$ai] = $value;
                } else {
                    $buffer[] = $value;
                }
            }
        }

        return new Barcode($data, $codeType, $foundAIs, $buffer, (string)$fnc1Prefix);
    }

    private function fetchFNC1Prefix(string $data): array
    {
        foreach ($this->config->getFnc1PrefixMap() as $prefix => $codeType) {
            if (substr_compare($data, $prefix, 0, strlen($prefix), true) === 0) {
                return [$prefix, $codeType];
            }
        }

        return [null, Barcode::TYPE_UNKNOWN];
    }

    private function fetchFixedAI(string $data, int $position, int $dataLength): array
    {
        if ($dataLength - $position < self::FIXED_AI_LENGTH) {
            return [null, null];
        }

        $ai = substr($data, $position, self::FIXED_AI_LENGTH);

        $length = self::FIXED_LENGTH_AIS[$ai] ?? null;

        if ($length === null) {
            return [null, null];
        }

        return [$ai, $length];
    }

    private function fetchKnownAI(string $data, int $position): array
    {
        foreach ($this->config->getKnownAIs() as $ai) {
            $aiLength = strlen($ai);
            if (substr_compare($data, $ai, $position, $aiLength, true) === 0) {
                return [substr($data, $position, $aiLength), $aiLength];
            }
        }

        return [null, null];
    }

    private function assertValueIsValid(string $value): void
    {
        $unencodableCharacters = array_diff(str_split($value), self::ENCODABLE_VALUE_CHARACTERS_SET);

        if (count($unencodableCharacters) > 0) {
            throw InvalidBarcodeException::becauseValueContainsInvalidCharacters(...$unencodableCharacters);
        }
    }
}
