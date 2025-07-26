<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DatePicker;
use backend\models\Salesman;

/* @var $this yii\web\View */
/* @var $model backend\models\Expense */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="expense-form">

<div class="row">
<div class="col-md-8 col-sm-12">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date')->widget(DatePicker::classname(), [
        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
        'readonly' => true,
        'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd'],
    ]); ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'person_in_charge')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'salesman_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Salesman::find()->all(), 'id', 'name'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => ['allowClear' => true],
    ]); ?>

    
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
