<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\IncomingItem */

$this->title = 'Update Incoming Item: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Incoming Item', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="incoming-item-update box-- box-warning--">

    <!-- <div class="box-header"></div> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
