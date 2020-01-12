<?php

require_once __DIR__ . '/bootstrap.php';

use CViniciusSDias\RecargaTvExpress\Service\SalesFinder;
use CViniciusSDias\RecargaTvExpress\Service\SerialCodeSender;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/** @var ContainerInterface $container */
$container = require_once __DIR__ . '/config/dependencies.php';

try {
    /** @var SalesFinder $emailsFinder */
    $emailsFinder = $container->get(SalesFinder::class);
    /** @var SerialCodeSender $codeSender */
    $codeSender = $container->get(SerialCodeSender::class);

    $sales = $emailsFinder->findSales();

    foreach ($sales as $sale) {
        $codeSender->sendCodeTo($sale);
    }
} catch (\Throwable $error) {
    /** @var LoggerInterface $logger */
    $logger = $container->get(LoggerInterface::class);
    $logger->error('Erro ao enviar códigos.', ['mensagem' => $error->getMessage(), 'erro' => $error]);
}
