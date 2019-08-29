<?php

namespace CViniciusSDias\RecargaTvExpress\Repository;

use CViniciusSDias\RecargaTvExpress\Model\Code;
use CViniciusSDias\RecargaTvExpress\Model\Sale;
use CViniciusSDias\RecargaTvExpress\Model\VO\Email;
use PDO;

class CodeRepository
{
    private $con;

    public function __construct(PDO $con)
    {
        $this->con = $con;
    }

    public function findUnusedCodeFor(Sale $sale): Code
    {
        $sql = 'SELECT id, serial FROM serial_codes WHERE user_email IS NULL AND product = ? LIMIT 1;';
        $stm = $this->con->prepare($sql);
        $stm->bindValue(1, $sale->product);
        $stm->execute();

        $serialCode = $stm->fetch(PDO::FETCH_NUM);
        if (false === $serialCode) {
            throw new \DomainException('No unused code found for this sale');
        }

        return new Code(...$serialCode);
    }

    public function attachUserToCode(Code $serialCode, Email $userEmail): bool
    {
        $sql = 'UPDATE serial_codes SET user_email = ? WHERE id = ?;';
        $stm = $this->con->prepare($sql);
        $stm->bindValue(1, $userEmail);
        $stm->bindValue(2, $serialCode->id, PDO::PARAM_INT);

        return $stm->execute();
    }

    public function findNumberOfAvailableCodes()
    {
        $sql = 'SELECT product, COUNT(id) AS total FROM serial_codes WHERE user_email IS NULL GROUP BY product;';
        $stm = $this->con->query($sql);

        return $stm->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
