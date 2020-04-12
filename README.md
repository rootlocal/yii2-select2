yii2-select2
============

* [Source code](https://github.com/rootlocal/yii2-select2)

## Install
```
composer require rootlocal/yii2-select2
```
or add

```json
"rootlocal/yii2-select2": "dev-master"
```

to the require section of your composer.json.

#### Basic usage:

```php
<?php
use yii\web\View;
use yii\bootstrap4\ActiveForm;
use rootlocal\widgets\select2\Select2Widget;

/**
 * @var View $this
 * @var ActiveForm $form 
 */
?>

<?= $form->field($model, 'attribute')->widget(Select2Widget::class,['items'=> $model->getItems()]) ?>

```
