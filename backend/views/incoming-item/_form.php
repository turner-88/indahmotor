<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use backend\models\Incoming;
use backend\models\Item;

/* @var $this yii\web\View */
/* @var $model backend\models\IncomingItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="incoming-item-form">

<div class="row">
<div class="col-md-8 col-sm-12">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'incoming_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Incoming::find()->all(), 'id', 'name'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => ['allowClear' => true],
    ]); ?>

    <?= $form->field($model, 'item_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Item::find()->all(), 'id', 'name'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => ['allowClear' => true],
    ]); ?>

    <?= $form->field($model, 'quantity')->textInput() ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'subtotal')->textInput() ?>

    <?= $form->field($model, 'discount')->textInput() ?>

    <?= $form->field($model, 'gross_price')->textInput() ?>

    
    <div class="form-panel">
        <div class="row">
    	    <div class="col-sm-12">
    	        <?= Html::submitButton('<i class="glyphicon glyphicon-ok"></i> ' . ($model->isNewRecord ? 'Create' : 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
	    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>

</div>
