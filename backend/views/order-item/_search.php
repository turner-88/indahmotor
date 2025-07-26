<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderItemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-item-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'order_id') ?>

    <?= $form->field($model, 'item_name') ?>

    <?= $form->field($model, 'quantity') ?>

    <?= $form->field($model, 'to_be_ordered') ?>

    <?php // echo $form->field($model, 'supplier_id') ?>

    <?php // echo $form->field($model, 'item_id') ?>

    <?php // echo $form->field($model, 'item_shortcode') ?>

    <?php // echo $form->field($model, 'brand_storage') ?>

    <?php // echo $form->field($model, 'brand_supplier') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'unit_of_measurement') ?>

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
