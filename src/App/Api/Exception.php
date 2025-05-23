<?php

declare(strict_types=1);

namespace App\Api;

use Exception as BaseException;

/**
 * Custom exception class for API errors.
 */
class ApiException extends BaseException
{
    private int $httpStatusCode;
    private string $userMessage;

    /**
     * Constructor for API Exception.
     *
     * @param string $userMessage Message to display to the end user
     * @param int $httpStatusCode HTTP status code for the response
     * @param string $internalMessage Internal message for logging/debugging (optional)
     * @param int $code Exception code (optional)
     * @param BaseException|null $previous Previous exception (optional)
     */
    public function __construct(
        string $userMessage,
        int $httpStatusCode = 500,
        string $internalMessage = '',
        int $code = 0,
        ?BaseException $previous = null
    ) {
        $this->httpStatusCode = $httpStatusCode;
        $this->userMessage = $userMessage;
        parent::__construct($internalMessage ?: $userMessage, $code, $previous);
    }

    /**
     * Get the HTTP status code for the exception.
     *
     * @return int
     */
    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    /**
     * Get the user-friendly message.
     *
     * @return string
     */
    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    /**
     * Format the exception as a JSON response array.
     *
     * @return array
     */
    public function toJsonResponse(): array
    {
        return [
            'error' => $this->userMessage,
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ];
    }
}