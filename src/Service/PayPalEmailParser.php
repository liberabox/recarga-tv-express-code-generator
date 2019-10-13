<?php

namespace CViniciusSDias\RecargaTvExpress\Service;

use CViniciusSDias\RecargaTvExpress\Model\Sale;
use CViniciusSDias\RecargaTvExpress\Model\VO\Email;
use PhpImap\IncomingMail;

class PayPalEmailParser extends EmailParser
{
    protected function canParse(IncomingMail $email): bool
    {
        return $email->fromAddress === 'service@paypal.com.br';
    }

    protected function parseEmail(IncomingMail $email): ?Sale
    {
        $domDocument = new \DOMDocument();
        libxml_use_internal_errors(true);
        $domDocument->loadHTML($email->textHtml);
        $xPath = new \DOMXPath($domDocument);

        $productQuery = '//div/div/div/blockquote/div/table/tbody/tr/td[2]/table[1]/tbody/tr/td/table/tbody/tr[3]/td/table[2]/tbody/tr/td[2]/div/table[3]/tbody/tr[2]/td[1]';
        $dataNodes = $xPath->query($productQuery);
        preg_match('/Tv express (mensal|anual)/i', $dataNodes->item(0)->textContent, $matches);
        $product = $matches[1];

        $emailQuery = '//div/div/div/blockquote/div/table/tbody/tr/td[2]/table[1]/tbody/tr/td/table/tbody/tr[3]/td/table[2]/tbody/tr/td[2]/div/span[3]/span/a';
        $emailAddress = $xPath->query($emailQuery)
            ->item($dataNodes->length - 1)
            ->textContent;

        return new Sale(new Email($emailAddress), $product);
    }
}
