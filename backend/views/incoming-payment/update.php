<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\IncomingPayment */

$this->title = 'Update Incoming Payment: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Incoming Payment', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="incoming-payment-update box-- box-warning--">

    <!-- <div class="box-header"></div> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
