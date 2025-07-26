<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ViewItemPrice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="view-item-price-form">

<div class="row">
<div class="col-md-8 col-sm-12">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'nama_barang')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'kode_barang')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'merk')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tipe')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'satuan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'stok')->textInput() ?>

    <?= $form->field($model, 'lokasi_penyimpanan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'harga_list')->textInput() ?>

    <?= $form->field($model, 'diskon_pembelian')->textInput() ?>

    <?= $form->field($model, 'harga_net')->textInput() ?>

    <?= $form->field($model, 'diskon_A')->textInput() ?>

    <?= $form->field($model, 'harga_A')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'diskon_B')->textInput() ?>

    <?= $form->field($model, 'harga_B')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'diskon_C')->textInput() ?>

    <?= $form->field($model, 'harga_C')->textInput(['maxlength' => true]) ?>

    
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
