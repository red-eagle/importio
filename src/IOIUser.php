<?php

namespace RedEagle\ImportIO;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use RedEagle\ImportIO\Exception\APIKeyException;
use RedEagle\ImportIO\Exception\AuthException;

/**
 * Class IOIUser
 *
 * @package RedEagle\ImportIO
 */
class IOIUser
{
    /**
     * @var
     */
    protected $apiKey;

    /**
     * IOIUser constructor.
     * @param $apiKey
     * @throws AuthException
     */
    public function __construct($apiKey)
    {
        if ($this->_checkKey($apiKey)) {
            $this->apiKey = $apiKey;
        }
    }

    /**
     * Проверка ключа
     *
     * @param $apiKey
     * @throws APIKeyException
     * @return bool
     */
    private function _checkKey($apiKey)
    {
        //Проверяем соответствие формата
        if (false == preg_match('/^[a-z0-9]{160}$/', $apiKey)) {
            throw new APIKeyException('Неправильный ключ API. Ключ должен состоять из 160 цифр и строчных латинских букв.');
        }
        //Проверяем существование пользователя
        $HTTPClient = new Client([
            'base_uri' => 'https://api.import.io/auth/currentuser'
        ]);
        try {
            $response = $HTTPClient->get("", [
                'query' => [
                    '_apikey' => $apiKey
                ]
            ]);
            $code = $response->getStatusCode();
        } catch (ClientException $e) {
            $responseBody = \json_decode($e->getResponse()->getBody(), true);
            throw new AuthException('Ошибка авторизации: ' . $responseBody['error'], 404);
        }
        if ($code == 200) return true;
        else return false;
    }

    /**
     * Стандартные параметры для отправки запроса.
     * @return array
     */
    public function getQuery()
    {
        return [
            '_apikey' => $this->apiKey
        ];
    }

}