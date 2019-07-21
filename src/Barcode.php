<?php

declare(strict_types=1);

namespace Lamoda\GS1Parser;

final class Barcode
{
    public const TYPE_GS1_DATAMATRIX = 'gs1_datamatrix';
    public const TYPE_GS1_QRCODE = 'gs1_qrcode';
    public const TYPE_EAN = 'ean';
    public const TYPE_GS1_128 = 'gs1_128';
    public const TYPE_UNKNOWN = 'unknown';

    /**
     * @var string
     */
    private $content;
    /**
     * @var string
     */
    private $type;
    /**
     * @var array
     */
    private $ais;
    /**
     * @var array
     */
    private $buffer;
    /**
     * @var string | null
     */
    private $fnc1Prefix;

    public function __construct(string $content, string $type, array $ais, array $buffer, string $fnc1Prefix)
    {
        $this->content = $content;
        $this->type = $type;
        $this->ais = $ais;
        $this->buffer = $buffer;
        $this->fnc1Prefix = $fnc1Prefix;
    }


    public function type(): string
    {
        return $this->type;
    }

    public function ais(): array
    {
        return $this->ais;
    }

    public function buffer(): array
    {
        return $this->buffer;
    }

    public function hasAI(string $code): bool
    {
        return array_key_exists($code, $this->ais);
    }

    public function ai(string $code): string
    {
        return $this->ais[$code] ?? '';
    }

    public function raw(): string
    {
        return $this->content;
    }

    public function fnc1Prefix(): string
    {
        return $this->fnc1Prefix;
    }

    public function normalized(): string
    {
        $prefixLength = strlen($this->fnc1Prefix);
        return substr($this->content, $prefixLength);
    }
}