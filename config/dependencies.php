<?php

use CViniciusSDias\RecargaTvExpress\Service\{EmailParser, MercadoPagoEmailParser, PayPalEmailParser};
use CViniciusSDias\RecargaTvExpress\Service\SerialCodeGenerator;
use CViniciusSDias\RecargaTvExpress\Service\SerialCodeSender;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\TelegramBotHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use function DI\{factory, get, env, create};
use PhpImap\Mailbox;
use Psr\Container\ContainerInterface;
use Symfony\Component\Mailer\Bridge\Google\Smtp\GmailTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;

$builder = new ContainerBuilder();

$builder->addDefinitions([
    'imapPath' => env('IMAP_PATH'),
    'imapLogin' => env('IMAP_LOGIN'),
    'imapPassword' => env('IMAP_PASSWORD'),
    'dsn' => env('DB_DSN'),
    'dbPassword' => env('DB_PASSWORD'),
    'dbUser' => env('DB_USER'),
    'logPath' => env('LOG_PATH'),
    'telegramApiKey' => env('TELEGRAM_API_KEY'),
    'telegramChannelId' => env('TELEGRAM_CHANNEL_ID'),
    \PDO::class => factory(function (ContainerInterface $c) {
        $password = $c->has('dbPassword') ? $c->get('dbPassword') : null;
        $user = $c->has('dbUser') ? $c->get('dbUser') : null;
        $dsn = $c->get('dsn');

        return new \PDO($dsn, $user, $password);
    }),
    Mailbox::class => create()
        ->constructor(get('imapPath'), get('imapLogin'), get('imapPassword'))
        ->method('setAttachmentsIgnore', true),
    GmailTransport::class => create()
        ->constructor(get('imapLogin'), get('imapPassword')),
    Mailer::class => create(Mailer::class)->constructor(get(GmailTransport::class)),
    MailerInterface::class => get(Mailer::class),
    SerialCodeSender::class => create()
        ->constructor(get(MailerInterface::class), get(SerialCodeGenerator::class), get('imapLogin')),
    EmailParser::class => factory(function (ContainerInterface $c) {
        $nullParser = new class extends EmailParser
        {
            protected function parseEmail(\PhpImap\IncomingMail $email): ?\CViniciusSDias\RecargaTvExpress\Model\Sale
            {
                return null;
            }

            protected function canParse(\PhpImap\IncomingMail $email): bool
            {
                return true;
            }
        };
        return new MercadoPagoEmailParser(new PayPalEmailParser($nullParser));
    }),
    LoggerInterface::class => factory(function (ContainerInterface $container) {
        $logger = new Logger('errors');
        $telegramBotHandler = new TelegramBotHandler(
            $container->get('telegramApiKey'),
            $container->get('telegramChannelId'),
            Logger::ERROR
        );
        $streamHandler = new StreamHandler($container->get('logPath'));

        return $logger
            ->pushHandler($streamHandler)
            ->pushHandler($telegramBotHandler);
    }),
]);

return $builder->build();
