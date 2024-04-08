<?php

namespace rootlocal\widgets\select2;

use Closure;
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
    public string $theme = self::THEME_DEFAULT;
    /** @var null|string Language code */
    public ?string $language = null;
    /** @var null|string A placeholder value can be defined and will be displayed until a selection is made */
    public ?string $placeholder;
    /*** @var null|boolean Multiple select boxes */
    public ?bool $multiple = null;
    /** @var null|boolean Tagging support */
    public ?bool $tags = null;
    /** @var string[] the JavaScript event handlers. */
    public array $events = [];
    /**
     * @link https://select2.github.io/options.html
     * @var array
     */
    public array $settings = [];
    /**
     * Array data
     * @example [['id'=>1, 'text'=>'enhancement'], ['id'=>2, 'text'=>'bug']]
     * @var null|array
     */
    public ?array $data = null;
    /**
     * You can use Select2Action to provide AJAX data
     * @var array|string
     */
    public $ajax;
    /**
     * @var array|Closure
     */
    public $items = [];
    /** @var null|string */
    public ?string $prompt = null;

    private ?string $_hash = null;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if ($this->language === null) {
            $this->language = Yii::$app->language;
        }

        if ($this->items instanceof Closure) {
            if ($this->hasModel()) {
                $this->items = call_user_func($this->items, $this->model, $this->attribute);
            } else {
                $this->items = call_user_func($this->items);
            }
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

        if (!empty($this->language)) {
            $this->settings['language'] = $this->language;
        }

        if (!empty($this->theme)) {
            $this->settings['theme'] = $this->theme;
        }

        if (!empty($this->placeholder)) {
            $this->settings['placeholder'] = $this->placeholder;
        }

        $js = Json::htmlEncode($this->settings);

        if (!empty($this->events)) {
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
                    $this->options['name'] ?? Html::getInputName($this->model, $this->attribute);
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
    public function renderWidget(): string
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
    public function run(): string
    {
        return $this->renderWidget();
    }
}
