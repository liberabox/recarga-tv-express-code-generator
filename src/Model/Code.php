<?php

namespace CViniciusSDias\RecargaTvExpress\Model;

use CViniciusSDias\RecargaTvExpress\Model\VO\Email;

/**
 * @property-read int $id
 * @property-read string $serial
 * @property-read Email $userEmail
 */
class Code
{
    use PropertyAccess;

    private int $id;
    private string $serial;
    private ?Email $userEmail;

    public function __construct(int $id, string $serial, Email $userEmail = null)
    {
        $this->id = $id;
        $this->serial = $serial;
        $this->userEmail = $userEmail;
    }

    public function __toString(): string
    {
        return $this->serial;
    }
}
