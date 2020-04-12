<?php

namespace rootlocal\widgets\select2;

use yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\InputWidget;
use yii\helpers\Json;
use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

/**
 * Class Select2Widget
 * @author Alexander Zakharov <sys@eml.ru>
 * @package rootlocal\widgets\select2
 */
class Select2Widget extends InputWidget
{
    /** @var string */
    public const THEME_DEFAULT = 'bootstrap';

    /** @var string */
    public const PLUGIN_NAME = 'select2';

    /** @var string */
    public $theme = self::THEME_DEFAULT;
    /** @var string Language code */
    public $language;
    /** @var string A placeholder value can be defined and will be displayed until a selection is made */
    public $placeholder;
    /*** @var boolean Multiple select boxes */
    public $multiple;
    /** @var boolean Tagging support */
    public $tags;
    /** @var string[] the JavaScript event handlers. */
    public $events = [];
    /**
     * @link https://select2.github.io/options.html
     * @var array
     */
    public $settings = [];
    /**
     * Array data
     * @example [['id'=>1, 'text'=>'enhancement'], ['id'=>2, 'text'=>'bug']]
     * @var array
     */
    public $data;
    /**
     * You can use Select2Action to provide AJAX data
     * @see \yii\helpers\BaseUrl::to()
     * @var array|string
     */
    public $ajax;
    /**
     * @see \yii\helpers\BaseArrayHelper::map()
     * @var array
     */
    public $items = [];
    /** @var string */
    public $prompt;

    /** @var string */
    private $_hash;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->language === null) {
            $this->language = Yii::$app->language;
        }

        if ($this->prompt !== null) {
            $this->items = ArrayHelper::merge([null => $this->prompt], $this->items);
        }

        $view = $this->getView();
        $this->buildCssOptions();
        $this->registerAssets($view);
        $this->buildJsOptions($view);
        $this->registerJs($view);
    }

    /**
     * @param View $view
     */
    public function buildJsOptions(View $view)
    {
        $this->settings['width'] = '100%';

        if ($this->language !== null) {
            $this->settings['language'] = $this->language;
        }

        if ($this->theme !== null) {
            $this->settings['theme'] = $this->theme;
        }

        if ($this->placeholder !== null) {
            $this->settings['placeholder'] = $this->placeholder;
        }

        $js = Json::htmlEncode($this->settings);

        if ($this->events !== null) {
            foreach ($this->events as $event => $handler) {
                $js .= '.on("' . $event . '", ' . new JsExpression($handler) . ')';
            }
        }

        $this->_hash = self::PLUGIN_NAME . '_' . hash('crc32', get_called_class() . $js);
        $view->registerJs("var {$this->_hash} = {$js};", $view::POS_HEAD, $this->_hash);
    }

    /**
     * Build css options
     */
    public function buildCssOptions()
    {
        if (!isset($this->options['class'])) {
            $this->options['class'] = 'form-control ';
        }

        if ($this->tags !== null) {
            $this->options['data-tags'] = 'true';
            $this->options['multiple'] = true;
        }

        if (!empty($this->ajax)) {
            $this->options['data-ajax--url'] = Url::to($this->ajax);
            $this->options['data-ajax--cache'] = 'true';
        }

        if ($this->multiple !== null) {
            $this->options['data-multiple'] = 'true';
            $this->options['multiple'] = true;
        }

        if (!empty($this->data)) {
            $this->options['data-data'] = Json::encode($this->data);
        }

        if ($this->multiple !== null || !empty($this->settings['multiple'])) {

            if ($this->hasModel()) {
                $name =
                    isset($this->options['name']) ? $this->options['name']
                        : Html::getInputName($this->model, $this->attribute);
            } else {
                $name = $this->name;
            }

            if (substr($name, -2) != '[]') {
                $this->options['name'] = $this->name = $name . '[]';
            }
        }
    }

    /**
     * Register Js
     * @param View $view
     */
    public function registerJs(View $view)
    {
        $js = 'jQuery("#' . $this->options['id'] . '").' . self::PLUGIN_NAME . '(' . $this->_hash . ');';
        $view->registerJs($js);
    }

    /**
     * Registers Assets
     * @param View $view
     * @throws InvalidConfigException
     */
    public function registerAssets(View $view)
    {
        $bundle = Select2VendorAsset::register($view);
        $languageDir = $bundle->baseUrl . '/js/i18n/';
        $languages = explode('-', $this->language);

        if (count($languages) > 0) {
            $this->language = $languages[0];
        }

        $languageFile = $languageDir . $this->language . '.js';
        $view->registerJsFile($languageFile, ['depends' => Select2Asset::class]);

        Select2ThemeBootstrap4Asset::register($view);
        Select2Asset::register($view);
    }

    /**
     * @return string
     */
    public function renderWidget()
    {
        if ($this->hasModel()) {
            return Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->options);
        }

        return Html::dropDownList($this->name, $this->value, $this->items, $this->options);
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function run()
    {
        return $this->renderWidget();
    }
}
