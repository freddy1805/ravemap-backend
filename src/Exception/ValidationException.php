<?php

namespace App\Exception;

class ValidationException extends \Exception {

    protected $message  = 'Validation failed';

    private array $result = [];

    public function __construct(array $result)
    {
        $this->result = $result;
        parent::__construct($this->message, $this->code, $this->getPrevious());
    }

    public function getResult(): ?array
    {
        return $this->result;
    }
}
