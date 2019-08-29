<?php

namespace CViniciusSDias\RecargaTvExpress\Tests\Repository;

use CViniciusSDias\RecargaTvExpress\Model\Sale;
use CViniciusSDias\RecargaTvExpress\Model\VO\Email;
use CViniciusSDias\RecargaTvExpress\Repository\CodeRepository;
use PHPUnit\Framework\TestCase;

class CodeRepositoryTest extends TestCase
{
    private static $con;

    public static function setUpBeforeClass(): void
    {
        $con = new \PDO('sqlite::memory:');
        $con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $con->exec('CREATE TABLE serial_codes (
            id INTEGER PRIMARY KEY,
            serial TEXT NOT NULL,
            user_email TEXT DEFAULT NULL,
            product TEXT
        );');

        self::$con = $con;
    }

    public static function tearDownAfterClass(): void
    {
        self::$con = null;
    }

    protected function tearDown(): void
    {
        self::$con->exec('DELETE FROM serial_codes;');
    }

    public function testSqlShouldFindOneUnusedCodeForASale()
    {
        // Arrange
        $this->insertCode('4321', 'anual');
        $this->insertCode('1111', 'anual');
        $this->insertCode('1234', 'mensal');

        $codeRepository = new CodeRepository(self::$con);
        $sale = new Sale(new Email('email@example.com'), 'mensal');

        // Act
        $code = $codeRepository->findUnusedCodeFor($sale);

        // Assert
        self::assertSame('1234', $code->serial);
    }

    public function testShouldFindExactNumberOfAvailableCodes()
    {
        // Arrange
        $this->insertCode('1111', 'anual');
        $this->insertCode('2222', 'anual');
        $this->insertCode('3333', 'mensal');
        $this->insertCode('4444', 'mensal');
        $this->insertCode('5555', 'mensal');

        $codeRepository = new CodeRepository(self::$con);
        $numberOfAvailableCodes = $codeRepository->findNumberOfAvailableCodes();

        self::assertEquals(2, $numberOfAvailableCodes['anual']);
        self::assertEquals(3, $numberOfAvailableCodes['mensal']);
    }

    private function insertCode(string $serial, string $product)
    {
        /** @var \PDOStatement $stm */
        $stm = self::$con->prepare('INSERT INTO serial_codes (serial, product) VALUES (?, ?)');
        $stm->bindValue(1, $serial);
        $stm->bindValue(2, $product);
        $stm->execute();
    }
}
