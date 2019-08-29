<?php

namespace CViniciusSDias\RecargaTvExpress\Tests\Service;

use CViniciusSDias\RecargaTvExpress\Model\Sale;
use CViniciusSDias\RecargaTvExpress\Model\VO\Email;
use CViniciusSDias\RecargaTvExpress\Repository\CodeRepository;
use CViniciusSDias\RecargaTvExpress\Service\SerialCodeGenerator;
use PHPUnit\Framework\TestCase;

class SerialCodeGeneratorTest extends TestCase
{
    public function testFailureOnAttachingUserMustThrowException()
    {
        $this->expectException(\DomainException::class);

        $pdoMock = $this->createMock(\PDO::class);
        $pdoMock->expects($this->once())
            ->method('beginTransaction');
        $pdoMock->expects($this->once())
            ->method('rollback');
        $pdoMock->expects($this->never())
            ->method('commit');

        // Return false represents an error on attachin User to Code
        $repositoryMock = $this->createMock(CodeRepository::class);
        $repositoryMock->expects($this->once())
            ->method('attachUserToCode')
            ->willReturn(false);

        $saleMock = $this->createMock(Sale::class);
        $saleMock->method('__get')
            ->with('costumerEmail')
            ->willReturn(new Email('test@email.com'));

        $codeGenerator = new SerialCodeGenerator($pdoMock, $repositoryMock);
        $codeGenerator->generateSerialCode($saleMock);
    }
}
