<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Order */

$this->title = 'Order: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Order', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-update box-- box-warning--">

    <!-- <div class="box-header"></div> -->

    <?= $this->render('_form-update-ajax', [
        'model' => $model,
        'modelItem' => $modelItem,
        'searchModelItemMaster' => $searchModelItemMaster,
        'dataProviderItemMaster' => $dataProviderItemMaster,
    ]) ?>

</div>
