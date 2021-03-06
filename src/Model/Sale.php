<?php

namespace CViniciusSDias\RecargaTvExpress\Model;

use CViniciusSDias\RecargaTvExpress\Model\VO\Email;

/**
 * @property-read Email $costumerEmail
 * @property-read string $product
 */
class Sale
{
    use PropertyAccess;

    private $costumerEmail;
    private $product;

    public function __construct(Email $costumerEmail, string $product)
    {
        $this->costumerEmail = $costumerEmail;
        $this->product = trim($product);
    }
}
