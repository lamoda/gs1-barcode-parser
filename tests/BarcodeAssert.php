<?php

declare(strict_types=1);

namespace Lamoda\GS1Parser\Tests;

use Lamoda\GS1Parser\Barcode;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;

trait BarcodeAssert
{
    /**
     * @param Barcode $expected
     * @param Barcode $actual
     *
     * @throws ExpectationFailedException
     */
    public static function assertBarcodesAreEqual(Barcode $expected, Barcode $actual): void
    {
        Assert::assertEquals($expected->raw(), $actual->raw(), 'Wrong RAW format');
        Assert::assertEquals($expected->type(), $actual->type(), 'Wrong type');
        Assert::assertEquals($expected->ais(), $actual->ais(), 'Wrong AIs list');
        Assert::assertEquals($expected->buffer(), $actual->buffer(), 'Wrong buffer');
    }
}