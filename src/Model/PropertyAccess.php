<?php


namespace CViniciusSDias\RecargaTvExpress\Model;

trait PropertyAccess
{
    public function __get($property)
    {
        return $this->$property;
    }
}
