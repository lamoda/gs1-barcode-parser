<?php

declare(strict_types=1);

namespace Lamoda\GS1Parser\Validator;

final class ValidatorConfig
{
    private $requiredAIs = [];
    private $forbiddenAIs = [];
    private $aiConstraints = [];
    private $allowEmpty = false;

    public function getRequiredAIs(): array
    {
        return $this->requiredAIs;
    }

    public function setRequiredAIs(array $requiredAIs): self
    {
        $this->requiredAIs = $requiredAIs;
        return $this;
    }

    public function getForbiddenAIs(): array
    {
        return $this->forbiddenAIs;
    }

    public function setForbiddenAIs(array $forbiddenAIs): self
    {
        $this->forbiddenAIs = $forbiddenAIs;
        return $this;
    }

    public function isAllowEmpty(): bool
    {
        return $this->allowEmpty;
    }

    public function setAllowEmpty(bool $allowEmpty): self
    {
        $this->allowEmpty = $allowEmpty;
        return $this;
    }

    public function setAIConstraints(array $aiConstraints): self
    {
        $this->aiConstraints = $aiConstraints;
        return $this;
    }

    public function getAIConstraints(): array
    {
        return $this->aiConstraints;
    }
}
