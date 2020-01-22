<?php

namespace CViniciusSDias\RecargaTvExpress\Model\VO;

final class Email
{
    private string $address;

    public function __construct(string $address)
    {
        if (false === filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("$address não é um endereço de e-mail válido");
        }

        $this->address = $address;
    }

    public function __toString()
    {
        return $this->address;
    }
}
