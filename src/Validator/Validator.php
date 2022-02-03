<?php

declare(strict_types=1);

namespace Lamoda\GS1Parser\Validator;

use Lamoda\GS1Parser\Exception\InvalidBarcodeException;
use Lamoda\GS1Parser\Parser\ParserInterface;

final class Validator implements ValidatorInterface
{
    /**
     * @var ParserInterface
     */
    private $parser;
    /**
     * @var ValidatorConfig
     */
    private $config;

    public function __construct(ParserInterface $parser, ValidatorConfig $config)
    {
        $this->parser = $parser;
        $this->config = $config;
    }

    public function validate($value): Resolution
    {
        if ($value === null && $this->config->isAllowEmpty()) {
            return Resolution::createValid();
        }

        if (!is_string($value)) {
            return Resolution::createInvalid([
                ErrorCodes::VALUE_IS_NOT_STRING => 'Value is not a string',
            ]);
        }

        $trimmedValue = trim($value);

        if ($trimmedValue === '') {
            return $this->config->isAllowEmpty() ?
                Resolution::createValid() :
                Resolution::createInvalid([
                    ErrorCodes::VALUE_EMPTY => 'Value is empty',
                ]);
        }

        try {
            $barcode = $this->parser->parse($trimmedValue);
        } catch (InvalidBarcodeException $exception) {
            return Resolution::createInvalid([
                ErrorCodes::INVALID_VALUE => sprintf(
                    'Value is invalid: %s',
                    $exception->getCode()
                ),
            ]);
        }

        $ais = array_keys($barcode->ais());
        $requiredAis = array_diff($this->config->getRequiredAIs(), $ais);
        $forbiddenAIs = array_intersect($this->config->getForbiddenAIs(), $ais);

        if (count($requiredAis) > 0) {
            return Resolution::createInvalid([
                ErrorCodes::MISSING_AIS => sprintf(
                    'AIs are missing: "%s"',
                    implode('", "', $requiredAis)
                ),
            ]);
        }

        if (count($forbiddenAIs) > 0) {
            return Resolution::createInvalid([
                ErrorCodes::FORBIDDEN_AIS => sprintf(
                    'AIs are forbidden: "%s"',
                    implode('", "', $forbiddenAIs)
                ),
            ]);
        }

        foreach ($this->config->getAIConstraints() as $code => $constraint) {
            if ($barcode->hasAI((string) $code)
                && !$constraint($ai = $barcode->ai((string) $code))
            ) {
                return Resolution::createInvalid([
                    ErrorCodes::INVALID_VALUE => sprintf(
                        'AI is invalid: code=%s, value=%s',
                        $code,
                        $ai
                    ),
                ]);
            }
        }

        return Resolution::createValid();
    }

}