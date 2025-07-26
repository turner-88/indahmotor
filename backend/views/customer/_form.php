<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use backend\models\PriceGroup;
use backend\models\Salesman;

/* @var $this yii\web\View */
/* @var $model backend\models\Customer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-form">

<div class="row">
<div class="col-md-8 col-sm-12">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textarea(['rows' => 2]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'person_in_charge')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price_group_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(PriceGroup::find()->all(), 'id', 'name'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => ['allowClear' => true],
    ]); ?>

    <?= $form->field($model, 'salesman_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Salesman::find()->all(), 'id', 'name'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => ['allowClear' => true],
    ]); ?>

    <?= $form->field($model, 'payment_limit_duration')->textInput() ?>
    
    <?= $form->field($model, 'initial_debt')->textInput() ?>

    
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
