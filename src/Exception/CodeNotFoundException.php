<?php

namespace CViniciusSDias\RecargaTvExpress\Exception;

use CViniciusSDias\RecargaTvExpress\Model\Sale;

class CodeNotFoundException extends \Exception
{
    private $sale;

    public function __construct($message, Sale $sale)
    {
        parent::__construct($message, 0, null);

        $this->sale = $sale;
    }

    public function sale(): Sale
    {
        return $this->sale;
    }
}
