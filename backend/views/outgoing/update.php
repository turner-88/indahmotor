<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Outgoing */

$this->title = 'Update Outgoing: ' . $model->serial;
$this->params['breadcrumbs'][] = ['label' => 'Outgoing', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->serial, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="outgoing-update box-- box-warning--">

    <!-- <div class="box-header"></div> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
