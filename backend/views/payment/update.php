<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Payment */

$this->title = 'Pembayaran: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pembayaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="payment-update box-- box-warning--">

    <!-- <div class="box-header"></div> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
