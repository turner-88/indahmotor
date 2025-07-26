<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Incoming */

$this->title = 'Update Incoming: ' . $model->serial;
$this->params['breadcrumbs'][] = ['label' => 'Incoming', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->serial, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="incoming-update box-- box-warning--">

    <!-- <div class="box-header"></div> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
