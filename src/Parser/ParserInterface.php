<?php

declare(strict_types=1);

namespace Lamoda\GS1Parser\Parser;

use Lamoda\GS1Parser\Barcode;

interface ParserInterface
{
    public function parse(string $barcode): Barcode;
}