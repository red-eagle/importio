<?php

namespace RedEagle\ImportIO;

use RedEagle\ImportIO\Exception\AuthException;
use RedEagle\ImportIO\Traits\ConnectionTrait;

/**
 * Class ImportIO
 *
 * Класс реализует простой интерфейс для доступа к ImportIo
 * Этот класс подходит только для авторизированных пользователей
 *
 * @package RedEagle\ImportIO
 */
class IOIClient
{
    use ConnectionTrait;
    const BASE_URL = 'https://api.import.io/';

    /**
     * @var IOIUser
     */
    protected $user;

    /**
     * ImportIO constructor.
     */
    function __construct(array $options)
    {
        //  Указываем пользователя.
        //  Можно передать экземпляр IOIUser или ключ API для создания экземпляра IOIUser
        if (empty($options['user']) || !($options['user'] instanceof IOIUser)) {
            if (empty($options['apiKey'])) {
                throw new AuthException("Для подключения обязательно нужно указать пользователя или ключ API");
            } else {
                $this->user = new IOIUser($options['apiKey']);
            }
        } else {
            $this->user = $options['user'];
        }
    }


    /**
     * @param $name
     * @return bool|mixed|IOIUser
     */
    public function __get($name)
    {
        //Можем извлекать объект IOIUser для повторного использования
        if ($name === 'user') {
            return $this->user;
        } else {
            return null;
        }
    }

    /**
     * Получаем список коннекторов пользователя
     */
    public function getConnectors()
    {
        $response = $this->_connection()->post('/store/connector/_search', [
            'form_params' => $this->user->getQuery()
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        $connectors = [];

        foreach ($data['hits']['hits'] as $hit) {
            $connectors[] = new IOIConnector([
                'user' => $this->user,
                'data' => $hit['fields']
            ]);
        }
        return $connectors;
    }

    /**
     * Формируем запрос
     * @param $options
     */
    private function _defaultOptions($options)
    {
        //TODO Формирование тела запроса для получения данных
    }

}