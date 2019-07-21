<?php

declare(strict_types=1);

namespace Lamoda\GS1Parser\Validator;

final class Resolution
{
    /**
     * @var bool
     */
    private $isValid;
    /**
     * @var array
     */
    private $errors;

    private function __construct(bool $isValid, array $errors)
    {
        $this->isValid = $isValid;
        $this->errors = $errors;
    }

    public static function createValid(): self
    {
        return new static(true, []);
    }

    public static function createInvalid(array $errors): self
    {
        return new static(false, $errors);
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

}