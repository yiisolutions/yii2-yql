<?php

namespace yiisolutions\yql;

use yii\base\Component;

/**
 * YQL client.
 *
 * @package yiisolutions\yql
 */
class Client extends Component
{
    public $endPoint = 'https://query.yahoapis.com/v1/yql';
    public $queryParamName = 'q';
    public $formatParamName = 'format';

    public function query($yql)
    {

    }

    private function sendHttpRequest($url)
    {

    }
}
