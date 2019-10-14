<?php

use CViniciusSDias\RecargaTvExpress\Service\{EmailParser, MercadoPagoEmailParser, PayPalEmailParser};
use CViniciusSDias\RecargaTvExpress\Service\SerialCodeGenerator;
use CViniciusSDias\RecargaTvExpress\Service\SerialCodeSender;
use DI\ContainerBuilder;
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
]);

return $builder->build();
