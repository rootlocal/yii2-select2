<?php

namespace rootlocal\widgets\select2;

use yii\base\BootstrapInterface;
use yii\base\Application;
use yii\i18n\PhpMessageSource;

/**
 * Class Bootstrap Application bootstrap process
 *
 * @see \yii\base\BootstrapInterface
 * @author Alexander Zakharov <sys@eml.ru>
 * @package rootlocal\widgets\select2
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        // add module I18N category
        if (!isset($app->i18n->translations['select2'])) {
            $app->i18n->translations['select2'] = [
                'class' => PhpMessageSource::class,
                'sourceLanguage' => 'en-US',
                'basePath' => __DIR__ . '/messages',
            ];
        }
    }
}