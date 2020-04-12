<?php

namespace rootlocal\widgets\select2\actions;

use Yii;
use yii\web\Response;
use yii\base\Action;
use Closure;

/**
 * Class Select2Action
 *
 * @property Closure $dataCallback PHP callback function to retrieve filtered data
 * @example
 * ```php
 *      function ($query) {
 *          // @var $query array|mixed
 *          return [
 *              'results' => [
 *                  ['id' => 1, 'text' => 'First Element'],
 *                  ['id' => 2, 'text' => 'Second Element'],
 *              ]
 *          ];
 *      }
 * ```
 * @property array|mixed $query
 *
 * @author Alexander Zakharov <sys@eml.ru>
 */
class Select2Action extends Action
{
    /** @var string Name of the GET parameter */
    public $paramName = 'q';
    /** @var array */
    private $_dataCallback = [];
    /** @var array|mixed */
    private $_query;

    /**
     * @return array
     */
    public function getDataCallback()
    {
        return $this->_dataCallback;
    }

    /**
     * @param Closure $callback
     */
    public function setDataCallback(Closure $callback)
    {
        if ($callback instanceof Closure) {
            $result = call_user_func($callback, $this->getQuery());

            if (is_array($result)) {
                $this->_dataCallback = $result;
            }
        }
    }

    /**
     * @return array|mixed
     */
    public function getQuery()
    {
        if ($this->_query === null) {
            $this->_query = Yii::$app->request->get($this->paramName);
        }

        return $this->_query;
    }

    /**
     * @return array|mixed
     */
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->controller->enableCsrfValidation = false;

        $data = $this->getDataCallback();

        if (!isset($data['results'])) {
            $data = ['results' => $data];
        }

        return $data;
    }
}