<?php

namespace yiisolutions\yql;

use yii\base\Component;
use yii\base\Exception;
use yii\helpers\Json;
use yii\httpclient\Client as HttpClient;

/**
 * YQL client.
 *
 * Usage:
 *
 * 1. Enable component in configuration file
 *
 * ```php
 * 'components' => [
 *     'yqlExchange' => [
 *          'class' => 'yiisolutions\yql\Client',
 *          //'baseUri' => 'https://query.yahoapis.com/v1/yql',
 *          'env' => 'store://datatables.org/alltableswithkeys',
 *          'diagnosticsMode' => false,
 *          'debugMode' => false,
 *     ],
 * ],
 * ```
 *
 * and fetch data in code
 *
 * ```php
 * public function actionIndex()
 * {
 *     $yqlQuery = 'select * from yahoo.finance.xchange where pair in ("EURUSD","GBPUSD")';
 *     $exchangeResult = Yii::$app->get('yqlExchange')->query($yql);
 *     if (isset($exchangeResult['rate'])) {
 *         // process results
 *     }
 * }
 * ```
 *
 * @package yiisolutions\yql
 */
class Client extends Component
{
    /**
     * @var string YQL Web Service base uri.
     */
    public $baseUri = 'https://query.yahoapis.com/v1/public/yql';

    public $queryParamName = 'q';
    public $formatParamName = 'format';
    public $diagnosticsParaName = 'diagnostics';
    public $debugParamName = 'debug';
    public $envParamName = 'env';

    public $env;
    public $debugMode = false;
    public $diagnosticsMode = false;

    public function query($yql)
    {
        $params = $this->buildParams($yql);
        $data = $this->getData($this->baseUri . '?' . http_build_query($params));

        return $this->processResults($data);
    }

    private function processResults(array $data)
    {
        return isset($data['query']['results']) ? $data['query']['results'] : [];
    }

    private function buildParams($yql)
    {
        $params = [
            $this->queryParamName => $yql,
            $this->formatParamName => 'json',
        ];

        if ($this->diagnosticsMode) {
            $params[$this->diagnosticsParaName] = true;
        }

        if ($this->debugMode) {
            $params[$this->debugMode] = true;
        }

        return $params;
    }

    private function getData($url)
    {
        $response = (new HttpClient())->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->send();

        if (!$response->isOk) {
            $message = $response->content;
            try {
                $data = Json::decode($message);
                if (isset($data['error']['description'])) {
                    $message = $data['error']['description'];
                }
            } catch (\Exception $exception) { }

            throw new Exception("HTTP {$response->statusCode}: {$message}");
        }

        return Json::decode($response->content);
    }
}
