<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use kartik\datecontrol\DateControl;
use backend\models\Incoming;
use backend\models\Supplier;
use backend\models\Outgoing;
use backend\models\Customer;

/* @var $this yii\web\View */
/* @var $model backend\models\Payment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-form">

<div class="row">
<div class="col-md-8 col-sm-12">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'incoming_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Incoming::find()->all(), 'id', 'id'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => ['allowClear' => true],
    ]); ?>

    <?= $form->field($model, 'supplier_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Supplier::find()->all(), 'id', 'name'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => ['allowClear' => true],
    ]); ?>

    <?= $form->field($model, 'outgoing_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Outgoing::find()->all(), 'id', 'id'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => ['allowClear' => true],
    ]); ?>

    <?= $form->field($model, 'customer_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Customer::find()->all(), 'id', 'shortTextWithDebt'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => ['allowClear' => true],
    ]); ?>

    <?= $form->field($model, 'date')->widget(DateControl::classname(), [
        'type' => DateControl::FORMAT_DATE,
        'readonly' => true,
        'ajaxConversion' => false,
        'widgetOptions' => [
            'pluginOptions' => [
                'autoclose' => true
            ]
        ]
    ]); ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'adjustment')->textInput() ?>

    <?= $form->field($model, 'return')->textInput() ?>

    <?= $form->field($model, 'remark')->textInput(); ?>

    
    <div class="form-panel">
        <div class="row">
    	    <div class="col-sm-12">
    	        <?= Html::submitButton('<i class="glyphicon glyphicon-ok"></i> ' . ($model->isNewRecord ? Yii::t('app', 'Simpan') : Yii::t('app', 'Update')), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
	    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>

</div>
