<?php

namespace Lunanimous\Rpc;

use Psr\Http\Message\ResponseInterface;

class NimiqResponse
{
    /**
     * Original response.
     *
     * @var ResponseInterface
     */
    protected $original;

    /**
     * Data response.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Constructs new json response.
     */
    public function __construct(ResponseInterface $response)
    {
        $this->original = $response;
        $this->data = json_decode((string) $response->getBody(), true);
    }

    /**
     * Checks if response has error.
     */
    public function hasError(): bool
    {
        return isset($this->data['error']);
    }

    /**
     * Gets error array.
     */
    public function getError(): array
    {
        return $this->data['error'];
    }

    /**
     * Checks if response has result.
     */
    public function hasResult(): bool
    {
        return isset($this->data['result']);
    }

    /**
     * Gets result array.
     */
    public function getResult()
    {
        return $this->data['result'];
    }

    /**
     * Gets the original response.
     */
    public function getOriginal(): ResponseInterface
    {
        return $this->original;
    }
}
