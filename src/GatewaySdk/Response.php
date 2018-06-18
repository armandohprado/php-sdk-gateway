<?php

namespace GatewaySdk;

use GatewaySdk\Domain\Response AS ResponseDomain;

class Response implements ResponseDomain
{
    /**
     * @var bool
     */
    private $isSuccess;

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $error;

    /**
     * @param bool       $isSuccess
     * @param array|null $data
     * @param array|null $error
     */
    function __construct(bool $isSuccess, array $data, array $error = null)
    {
        $this->data = $data;
        $this->error = $error;
        $this->isSuccess = $isSuccess;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->isSuccess;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }
}
