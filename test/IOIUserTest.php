<?php

namespace RedEagle\ImportIO\Tests;

use PHPUnit\Framework\TestCase;
use RedEagle\ImportIO\IOIUser;

class IOIUserTest extends TestCase
{
    public function testUserCreate()
    {
        $apiKey = include 'config/ioi.php';
        $user = new IOIUser($apiKey);
        return $user;
    }

    /**
     * @expectedException \RedEagle\ImportIO\Exception\APIKeyException
     * @expectedExceptionMessage Неправильный ключ API. Ключ должен состоять из 160 цифр и строчных латинских букв.
     */
    public function testUserAPIKeyException()
    {
        $user = new IOIUser('123456');
    }
}