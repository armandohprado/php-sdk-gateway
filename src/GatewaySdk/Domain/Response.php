<?php

namespace GatewaySdk\Domain;

interface Response
{

    /**
     * @param bool       $isSuccess
     * @param array|null $data
     * @param array|null $data
     */
    function __construct(bool $isSuccess, ?array $data, array $error);

    /**
     * @return bool
     */
    public function isSuccess(): bool;

    /**
     * @return array|null
     */
    public function getData(): ?array;

    /**
     * @return array|null
     */
    public function getError(): ?array;
}
