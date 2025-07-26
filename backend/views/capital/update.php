<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Capital */

$this->title = 'Update Pemasukan: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Pemasukan', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="capital-update box-- box-warning--">

    <!-- <div class="box-header"></div> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
