<?php

declare(strict_types=1);

namespace Lamoda\GS1Parser\Validator;

final class ErrorCodes
{
    public const VALUE_IS_NOT_STRING = 'c211cd00-71fe-4e35-a666-6f7d6ad8aa50';
    public const VALUE_EMPTY = '26e4f25e-7109-4ded-a460-12791e6f1413';
    public const INVALID_VALUE = '5ba19646-7e8d-4c8e-a282-0eadc12a9d9d';
    public const MISSING_AIS = '13a6b47f-7f45-4f40-b9a2-a0064e2284dd';
    public const FORBIDDEN_AIS = '8517a465-02ee-43b7-a824-8ce79ef4e3d8';
}