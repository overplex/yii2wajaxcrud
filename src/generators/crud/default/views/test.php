<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator \wodrow\wajaxcrud\generators\crud\Generator */
/* @var $model \yii\db\ActiveRecord */

$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form \kartik\form\ActiveForm */
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-test">
    <div class="row">
        <div class="col-sm-12"><?="<?= \$this->render(\"_detail-view\", ['model' => \$model]) ?>" ?></div>
        <div class="col-sm-12"></div>
    </div>
    <div class="col-sm-12">
        <div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form-test">
            <?="<?php " ?>$form = ActiveForm::begin(); ?><?="\n" ?>
<?php foreach ($generator->getColumnNames() as $attribute): ?>
<?php if (in_array($attribute, $safeAttributes)): ?>
            <?="<?=" . $generator->generateActiveField($attribute) . " ?>\n"; ?><?php endif; ?><?php endforeach; ?>
            <?='<?php if (!Yii::$app->request->isAjax){ ?>'."\n"?>
            <div class="form-group">
                <?= "<?= " ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Create') ?> : <?= $generator->generateString('Update') ?>, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
            <?="<?php } ?>\n"?>
            <?= "<?php " ?>ActiveForm::end(); ?>

        </div>
    </div>
</div>
