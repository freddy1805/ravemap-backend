<?php

namespace App\Exception;

use App\Entity\Event;

class ValidationException extends \Exception {

    protected $message  = 'Validation failed';

    private ?object $object;

    public function __construct(?object $object = null)
    {
        $this->object = $object;
        parent::__construct($this->message, $this->code, $this->getPrevious());
    }

    public function getObject(): ?object
    {
        return $this->object;
    }
}
