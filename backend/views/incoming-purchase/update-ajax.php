<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Incoming */

$this->title = 'Pembelian: ' . $model->id.' - '.$model->supplier->name;
$this->params['breadcrumbs'][] = ['label' => 'Pembelian', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="incoming-update box-- box-warning--">

    <!-- <div class="box-header"></div> -->

    <?= $this->render('_form-update-ajax', [
        'model' => $model,
        'modelItem' => $modelItem,
        'searchModelItemMaster' => $searchModelItemMaster,
        'dataProviderItemMaster' => $dataProviderItemMaster,
    ]) ?>

</div>
