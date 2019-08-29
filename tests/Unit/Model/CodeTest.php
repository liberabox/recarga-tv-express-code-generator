<?php

namespace CViniciusSDias\RecargaTvExpress\Tests\Unit\Model;

use CViniciusSDias\RecargaTvExpress\Model\Code;
use PHPUnit\Framework\TestCase;

class CodeTest extends TestCase
{
    public function testCastingCodeToStringMustReturnItsSerialCode()
    {
        $code = new Code(0, 'serial-test');
        self::assertSame('serial-test', (string) $code);
    }
}
