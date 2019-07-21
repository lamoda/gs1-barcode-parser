<?php

declare(strict_types=1);

namespace Lamoda\GS1Parser\Parser;

use Lamoda\GS1Parser\Barcode;
use Lamoda\GS1Parser\Constants;

final class ParserConfig
{
    /**
     * @var bool
     */
    private $fnc1SequenceRequired = true;
    /**
     * @var array
     */
    private $fnc1PrefixMap = [
        Constants::FNC1_GS1_DATAMATRIX_SEQUENCE => Barcode::TYPE_GS1_DATAMATRIX,
        Constants::FNC1_GS1_128_SEQUENCE => Barcode::TYPE_GS1_128,
        Constants::FNC1_GS1_QRCODE_SEQUENCE => Barcode::TYPE_GS1_QRCODE,
        Constants::FNC1_GS1_EAN_SEQUENCE => Barcode::TYPE_EAN,
    ];

    /**
     * @var string
     */
    private $groupSeparator = Constants::GROUP_SEPARATOR_SYMBOL;
    /**
     * @var array of strings
     */
    private $knownAIs = [];

    public function isFnc1SequenceRequired(): bool
    {
        return $this->fnc1SequenceRequired;
    }

    public function setFnc1SequenceRequired(bool $fnc1SequenceRequired): self
    {
        $this->fnc1SequenceRequired = $fnc1SequenceRequired;
        return $this;
    }

    public function getFnc1PrefixMap(): array
    {
        return $this->fnc1PrefixMap;
    }

    public function setFnc1PrefixMap(array $fnc1PrefixMap): self
    {
        $this->fnc1PrefixMap = $fnc1PrefixMap;
        return $this;
    }

    public function getGroupSeparator(): string
    {
        return $this->groupSeparator;
    }

    public function setGroupSeparator(string $groupSeparator): self
    {
        $this->groupSeparator = $groupSeparator;
        return $this;
    }

    public function getKnownAIs(): array
    {
        return $this->knownAIs;
    }

    public function setKnownAIs(array $knownAIs): self
    {
        $this->knownAIs = $knownAIs;
        return $this;
    }
}