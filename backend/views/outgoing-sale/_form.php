<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use kartik\datecontrol\DateControl;
use backend\models\OutgoingType;
use backend\models\Customer;
use backend\models\Storage;
use backend\models\Supplier;
use backend\models\ReturnPlan;
use backend\models\IncomingItem;
use backend\models\Salesman;

/* @var $this yii\web\View */
/* @var $model backend\models\Outgoing */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="outgoing-form">

<div class="row">
<div class="col-md-8 col-sm-12">

    <?php $form = ActiveForm::begin(); ?>

    <?= Html::activeHiddenInput($model, 'serial') ?>

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

    <?= $form->field($model, 'due_date')->widget(DateControl::classname(), [
        'type' => DateControl::FORMAT_DATE,
        'readonly' => true,
        'ajaxConversion' => false,
        'widgetOptions' => [
            'pluginOptions' => [
                'autoclose' => true
            ]
        ]
    ]); ?>

    <?= $form->field($model, 'customer_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Customer::find()->orderBy('name')->all(), 'id', 'shortText'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => ['allowClear' => true],
    ]); ?>

    <?= $form->field($model, 'salesman_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Salesman::find()->orderBy('name')->all(), 'id', 'name'),
        'options' => ['placeholder' => ''],
        'pluginOptions' => ['allowClear' => true],
    ]); ?>

    <?= $form->field($model, 'remark')->textInput(); ?>

    <div style="display: none;">
        <?= Html::checkbox('to_update_ajax', true, ['label' => 'lanjutkan ke halaman input item versi baru']); ?>
    </div>

    
    <div class="form-panel">
        <div class="row">
    	    <div class="col-sm-12">
    	        <?= Html::submitButton('<i class="glyphicon glyphicon-ok"></i> ' . ($model->isNewRecord ? 'Simpan' : 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
	    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>

</div>


<?php 
    /* $this->registerJsFile(
        '@web/js/outgoing-sale.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    ); */
?>