<?php

use CViniciusSDias\RecargaTvExpress\Repository\CodeRepository;
use CViniciusSDias\RecargaTvExpress\Service\CodesCountWarningSender;
use Psr\Container\ContainerInterface;

require_once __DIR__ . '/bootstrap.php';

/** @var ContainerInterface $container */
$container = require_once __DIR__ . '/config/dependencies.php';
/** @var CodeRepository $codeRepository */
$codeRepository = $container->get(CodeRepository::class);
$codes = $codeRepository->findNumberOfAvailableCodes();

$monthCount = $codes['mensal'] ?? 0;
$yearCount = $codes['anual'] ?? 0;

if ($monthCount < 30 || $yearCount < 15) {
    $container->get(CodesCountWarningSender::class)->sendWarning($monthCount, $yearCount);
}
