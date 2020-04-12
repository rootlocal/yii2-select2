<?php

namespace rootlocal\widgets\select2;

use yii\web\AssetBundle;

/**
 * Class Select2ThemeBootstrap4Asset
 * @author Alexander Zakharov <sys@eml.ru>
 * @package rootlocal\widgets\select2
 */
class Select2ThemeBootstrap4Asset extends AssetBundle
{
    /** @var array */
    public $css = [
        YII_DEBUG ? 'select2-bootstrap.css' : 'select2-bootstrap.min.css',
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
        $this->sourcePath = dirname(__FILE__) . '/assets/bootstrap4-theme/dist/';
    }
}