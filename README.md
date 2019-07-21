Lamoda GS1 Barcode parser and validator
=======================================

[![Build Status](https://travis-ci.org/lamoda/gs1-barcode-parser.svg?branch=master)](https://travis-ci.org/lamoda/gs1-barcode-parser)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lamoda/gs1-barcode-parser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lamoda/gs1-barcode-parser/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/lamoda/gs1-barcode-parser/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/lamoda/gs1-barcode-parser/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/lamoda/gs1-barcode-parser/badges/build.png?b=master)](https://scrutinizer-ci.com/g/lamoda/gs1-barcode-parser/build-status/master)


## Installation

### Composer

```sh
composer require lamoda/gs1-barcode-parser
```

## Description

This library provides parsing of GS1 Barcodes according to 
[GS1 General specification](https://www.gs1.org/sites/default/files/docs/barcodes/GS1_General_Specifications.pdf)
and [GS1 DataMatrix Guideline](https://www.gs1.org/docs/barcodes/GS1_DataMatrix_Guideline.pdf).

Library also provides general purpose validator for barcode's content.

## Usage

### Parser
```php
<?php

$config = new \Lamoda\GS1Parser\Parser\ParserConfig();
$parser = new \Lamoda\GS1Parser\Parser\Parser($config);

$value = ']d201034531200000111719112510ABCD1234';

$barcode = $parser->parse($value);

// $barcode is an object of Barcode class
```

### Validator
```php
<?php

$parserConfig = new \Lamoda\GS1Parser\Parser\ParserConfig();
$parser = new \Lamoda\GS1Parser\Parser\Parser($parserConfig);

$validatorConfig = new \Lamoda\GS1Parser\Validator\ValidatorConfig();
$validator = new \Lamoda\GS1Parser\Validator\Validator($parser, $validatorConfig);

$value = ']d201034531200000111719112510ABCD1234';

$resolution = $validator->validate($value);

if ($resolution->isValid()) {
    // ...
} else {
    var_dump($resolution->getErrors());
}

```