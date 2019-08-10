<?php

namespace CViniciusSDias\RecargaTvExpress\Service;

use CViniciusSDias\RecargaTvExpress\Model\Code;
use CViniciusSDias\RecargaTvExpress\Model\Sale;
use CViniciusSDias\RecargaTvExpress\Repository\CodeRepository;

class SerialCodeGenerator
{
    private $repository;
    private $con;

    public function __construct(\PDO $con, CodeRepository $repository)
    {
        $this->repository = $repository;
        $this->con = $con;
    }

    public function generateSerialCode(Sale $sale): Code
    {
        $this->con->beginTransaction();
        try {
            $code = $this->repository->findUnusedCodeFor($sale);
            $success = $this->repository->attachUserToCode($code, $sale->costumerEmail);
            if (!$success) {
                throw new \DomainException(
                    "Não foi possível relacionar o e-mail {$sale->costumerEmail} ao serial com ID {$code->id}"
                );
            }

            $this->con->commit();

            return $code;
        } catch (\Throwable $e) {
            $this->con->rollBack();
            throw $e;
        }
    }
}
