<?php

namespace RedEagle\ImportIO\Traits;

use GuzzleHttp\Client;

trait ConnectionTrait
{

    /**
     * @var Client
     */
    static $HTTPClient;

    /**
     * Получение Singleton-объекта соединения с api.import.io для выполнения запросов.
     * @return Client
     */
    private function _connection()
    {
        if (empty(self::$HTTPClient)) {
            static::$HTTPClient = new Client([
                'base_uri' => self::BASE_URL
            ]);
        }
        return static::$HTTPClient;
    }
}
