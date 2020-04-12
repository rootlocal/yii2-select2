<?php

namespace rootlocal\widgets\select2;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class Select2VendorAsset
 * @link https://select2.github.io/
 * @package rootlocal\widgets\select2
 */
class Select2VendorAsset extends AssetBundle
{
    /** @var string */
    public $sourcePath = '@vendor/select2/select2/dist';

    /** @var array */
    public $css = [
        YII_DEBUG ? 'css/select2.css' : 'css/select2.min.css',
    ];

    /** @var array */
    public $js = [
        YII_DEBUG ? 'js/select2.full.js' : 'js/select2.full.min.js',
    ];

    /** @var array */
    public $depends = [
        JqueryAsset::class,
    ];
}