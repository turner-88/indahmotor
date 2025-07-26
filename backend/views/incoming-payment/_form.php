<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DatePicker;
use backend\models\Incoming;
use backend\models\PaymentType;

/* @var $this yii\web\View */
/* @var $model backend\models\IncomingPayment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="incoming-payment-form">

<div class="row">
<div class="col-md-8 col-sm-12">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'incoming_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Incoming::find()->all(), 'id', 'serial'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => ['allowClear' => true],
    ]); ?>

    <?= $form->field($model, 'date')->widget(DatePicker::classname(), [
        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
        'readonly' => true,
        'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd'],
    ]); ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?php /* echo $form->field($model, 'payment_type_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(PaymentType::find()->all(), 'id', 'name'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => ['allowClear' => true],
    ]); */ ?>

    <?= $form->field($model, 'reference')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'image')->fileInput() ?>

    
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
