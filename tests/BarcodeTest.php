<?php

declare(strict_types=1);

namespace Lamoda\GS1Parser\Tests;

use Lamoda\GS1Parser\Barcode;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lamoda\GS1Parser\Barcode
 */
final class BarcodeTest extends TestCase
{
    public function testBarcode(): void
    {
        $barcode = new Barcode(']d201034531200000111719112510ABCD1234', Barcode::TYPE_GS1_DATAMATRIX, [
            '01' => '03453120000011',
        ], [
            '17191125',
            '10ABCD1234',
        ], ']d2');

        $this->assertEquals(Barcode::TYPE_GS1_DATAMATRIX, $barcode->type());
        $this->assertEquals(']d201034531200000111719112510ABCD1234', $barcode->raw());
        $this->assertEquals('01034531200000111719112510ABCD1234', $barcode->normalized());
        $this->assertEquals(']d2', $barcode->fnc1Prefix());
        $this->assertEquals([
            '01' => '03453120000011',
        ], $barcode->ais());
        $this->assertEquals([
            '17191125',
            '10ABCD1234',
        ], $barcode->buffer());
        $this->assertTrue($barcode->hasAI('01'));
        $this->assertFalse($barcode->hasAI('02'));
        $this->assertEquals('03453120000011', $barcode->ai('01'));
    }
}
