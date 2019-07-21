<?php

declare(strict_types=1);

namespace Lamoda\GS1Parser\Validator;

interface ValidatorInterface
{
    public function validate($barcode): Resolution;
}