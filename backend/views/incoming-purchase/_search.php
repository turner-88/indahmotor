<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\IncomingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="incoming-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'serial') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'due_date') ?>

    <?= $form->field($model, 'incoming_type_id') ?>

    <?php // echo $form->field($model, 'supplier_id') ?>

    <?php // echo $form->field($model, 'storage_id') ?>

    <?php // echo $form->field($model, 'customer_id') ?>

    <?php // echo $form->field($model, 'return_plan_id') ?>

    <?php // echo $form->field($model, 'outgoing_item_id') ?>

    <?php // echo $form->field($model, 'salesman_id') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
