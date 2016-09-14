<?php

namespace RedEagle\ImportIO;

use GuzzleHttp\Exception\ClientException;
use RedEagle\ImportIO\Exception\ConnectorException;
use RedEagle\ImportIO\Traits\ConnectionTrait;

/**
 * Class IOIConnector
 * Класс предоставляет интерфейс для коннектора ImportIO
 * TODO Покрыть тестами
 *
 * @package RedEagle\ImportIO
 */
class IOIConnector
{
    use ConnectionTrait;
    const BASE_URL = 'https://api.import.io/store/connector/';

    /**
     * @var string Имя коннектора
     */
    public $name;
    /**
     * @var string URL использовавщийся при настройке коннектора
     */
    public $source;
    /**
     * Домент для которого создавался коннектор
     *
     * @var string
     */
    public $domain;
    /**
     * @var IOIUser Класс пользователя ImportIO
     */
    private $user;
    /**
     * @var string ID коннектора
     */
    private $guid;

    /**
     * IOIConnector constructor.
     * @param $options array Опции для иницилизации.
     * Обязательно должны быть переданы пользователь и GUID коннектора.
     * @throws ConnectorException
     */
    public function __construct($options)
    {
        if (empty($options['user']) || !$options['user'] instanceof IOIUser) {
            throw new ConnectorException('User is required for Connector');
        } else {
            $this->user = $options['user'];
        }
        if (!@isset($options['guid']) && !@isset($options['data'])) {
            throw new ConnectorException('One of the option GUID or data required.');
        }
        $this->setProperties(@$options['guid'], @$options['data']);
    }

    /**
     * Метод устанавливает параметры коннектора.
     * Если проверка не прошла, то выбрасывается исключение
     * @param $guid string
     * @throws ConnectorException
     * @return IOIConnector
     */
    public function setProperties($guid, $data = null)
    {
        if (is_null($data)) {
            if (!preg_match('/[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/', $guid)) {
                throw new ConnectorException('Incorrect GUID');
            }
            try {
                $response = $this->_connection()->request('GET', $guid, [
                    'query' => $this->user->getQuery()
                ]);
                $data = \json_decode($response->getBody()->getContents(), true);
            } catch (ClientException $e) {
                $responseBody = \json_decode($e->getResponse()->getBody(), true);
                throw new ConnectorException($responseBody['error'], $e->getCode());
            }
        }
        $this->guid = $data['guid'];
        $this->name = $data['name'];
        $this->source = $data['source'];
        $this->domain = trim(strrev($data['reversedDomain']), '.');
        return $this;
    }

    /**
     * Возвращает извлеченные со страницы данные.
     * Формат отввета: http://api.docs.import.io/legacy/#!/Query_Methods/queryGet
     *
     * @param $url string Ссылка на страницу с которой нужно извлечь данные
     * @return array
     */
    public function getData($url)
    {
        $response = $this->_connection()->get($this->guid . '/_query', [
            'query' => $this->_getQuery($url)
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Получение параметров для запроса
     *
     * @return array
     */
    private function _getQuery($url)
    {
        $params = $this->user->getQuery();
        $params['format'] = 'json';
        $params['url'] = $url;
        return $params;
    }

}