<?php

declare(strict_types=1);

namespace Lamoda\GS1Parser;

final class Constants
{
    public const GROUP_SEPARATOR_SYMBOL = "\u{001d}";
    public const FNC1_SYMBOL = "\u{00e8}";

    public const FNC1_GS1_DATAMATRIX_SEQUENCE = ']d2';
    public const FNC1_GS1_QRCODE_SEQUENCE = ']Q3';
    public const FNC1_GS1_EAN_SEQUENCE = ']e0';
    public const FNC1_GS1_128_SEQUENCE = ']C1';
}