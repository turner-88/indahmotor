<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ItemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="item-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'shortcode') ?>

    <?= $form->field($model, 'brand') ?>

    <?= $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'unit_of_measurement_id') ?>

    <?php // echo $form->field($model, 'current_quantity') ?>

    <?php // echo $form->field($model, 'minimum_quantity') ?>

    <?php // echo $form->field($model, 'default_price') ?>

    <?php // echo $form->field($model, 'default_discount') ?>

    <?php // echo $form->field($model, 'purchase_net_price') ?>

    <?php // echo $form->field($model, 'purchase_gross_price') ?>

    <?php // echo $form->field($model, 'purchase_discount') ?>

    <?php // echo $form->field($model, 'description') ?>

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
