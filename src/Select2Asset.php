<?php

namespace rootlocal\widgets\select2;

use yii\web\AssetBundle;

/**
 * Class Select2Asset
 * @link https://select2.github.io/
 * @package rootlocal\widgets\select2
 */
class Select2Asset extends AssetBundle
{
    /** @var array */
    public $css = [
        'css/select2.css',
    ];

    /** @var array */
    public $js = [];

    /** @var array */
    public $depends = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->sourcePath = dirname(__FILE__) . '/assets/';
    }
}