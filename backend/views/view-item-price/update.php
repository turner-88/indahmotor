<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ViewItemPrice */

$this->title = 'Update View Item Price: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'View Item Price', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="view-item-price-update box-- box-warning--">

    <!-- <div class="box-header"></div> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
