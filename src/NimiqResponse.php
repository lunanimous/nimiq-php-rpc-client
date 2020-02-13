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
    protected $response;

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
        $this->response = $response;
        $this->data = json_decode((string) $response->getBody(), true);
    }

    /**
     * Gets the RPC id.
     */
    public function getId(): int
    {
        return $this->data['id'];
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
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
