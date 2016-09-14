<?php

namespace RedEagle\ImportIO\Tests;

use PHPUnit\Framework\TestCase;
use RedEagle\ImportIO\IOIConnector;
use RedEagle\ImportIO\IOIUser;

class IOIConnectorTest extends TestCase
{
    /**
     * @var IOIUser
     */
    private $user;
    private $dataForTest;

    public function setUp()
    {
        $this->dataForTest = (include 'config/connectorTestData.php');
        $this->user = new IOIUser($this->dataForTest['apiKey']);
        parent::setUp();
    }

    /**
     * Создание экземпляра коннектора по GUID
     */
    public function testCreationByGUID()
    {
        $connector = new IOIConnector([
            'user' => $this->user,
            'guid' => $this->dataForTest['guid']
        ]);
        return $connector;
    }

    /**
     * Создание экземпляра коннектора по данным коннектора
     */
    public function testCreationByData()
    {
        $connector = new IOIConnector([
            'user' => $this->user,
            'data' => $this->dataForTest['forCreateData']
        ]);
        $this->assertInstanceOf('RedEagle\ImportIO\IOIConnector', $connector);
    }

    /**
     * @expectedException \RedEagle\ImportIO\Exception\ConnectorException
     * @expectedExceptionMessage Incorrect GUID
     */
    public function testCreateByInvalidGUIDException()
    {
        $connector = new IOIConnector([
            'user' => $this->user,
            'guid' => '123456'
        ]);
    }

    /**
     * @expectedException \RedEagle\ImportIO\Exception\ConnectorException
     * @expectedExceptionCode 404
     * @expectedExceptionMessageRegExp /Bucket \[Connector\] does not contain GUID \[[0-9a-z-]+\]\./
     */
    public function testConnectorNotExists()
    {
        $connector = new IOIConnector([
            'user' => $this->user,
            'guid' => '9d166a5e-d91f-416d-a66d-2a51350ac33d'
        ]);
    }

    /**
     * @depends testCreationByGUID
     */
    public function testGetData($connector)
    {

    }
}