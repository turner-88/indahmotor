<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\OutgoingItem */

$this->title = 'Update Outgoing Item: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Outgoing Item', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="outgoing-item-update box-- box-warning--">

    <!-- <div class="box-header"></div> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
