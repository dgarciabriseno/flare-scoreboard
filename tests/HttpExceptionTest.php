<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use FlareScoreboard\Exceptions\HttpException;

final class HttpExceptionTest extends TestCase
{
    public function testGetBody(): void {
        $exception = new HttpException("Bad request", "Body");
        $this->assertEquals($exception->getContent(), "Body");
    }
}