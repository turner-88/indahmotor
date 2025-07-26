<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Outgoing */

$this->title = 'Penjualan: ' . $model->idText.' - '.$model->customer->name;
$this->params['breadcrumbs'][] = ['label' => 'Penjualan', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idText, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="outgoing-update box-- box-warning--">

    <!-- <div class="box-header"></div> -->

    <?= $this->render('_form-update-ajax', [
        'model' => $model,
        'modelItem' => $modelItem,
        'searchModelItemMaster' => $searchModelItemMaster,
        'dataProviderItemMaster' => $dataProviderItemMaster,
        'searchModelItemMasterReadyStock' => $searchModelItemMasterReadyStock,
        'dataProviderItemMasterReadyStock' => $dataProviderItemMasterReadyStock,
    ]) ?>

</div>
