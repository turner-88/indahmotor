<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ViewItemPriceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="view-item-price-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nama_barang') ?>

    <?= $form->field($model, 'kode_barang') ?>

    <?= $form->field($model, 'merk') ?>

    <?= $form->field($model, 'tipe') ?>

    <?php // echo $form->field($model, 'satuan') ?>

    <?php // echo $form->field($model, 'stok') ?>

    <?php // echo $form->field($model, 'lokasi_penyimpanan') ?>

    <?php // echo $form->field($model, 'harga_list') ?>

    <?php // echo $form->field($model, 'diskon_pembelian') ?>

    <?php // echo $form->field($model, 'harga_net') ?>

    <?php // echo $form->field($model, 'diskon_A') ?>

    <?php // echo $form->field($model, 'harga_A') ?>

    <?php // echo $form->field($model, 'diskon_B') ?>

    <?php // echo $form->field($model, 'harga_B') ?>

    <?php // echo $form->field($model, 'diskon_C') ?>

    <?php // echo $form->field($model, 'harga_C') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
