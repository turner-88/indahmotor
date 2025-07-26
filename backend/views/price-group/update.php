<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\PriceGroup */

$this->title = 'Update Price Group: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Price Group', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="price-group-update box-- box-warning--">

    <!-- <div class="box-header"></div> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
