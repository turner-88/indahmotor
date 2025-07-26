<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Expense */

$this->title = 'Update Pengeluaran: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Pengeluaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="expense-update box-- box-warning--">

    <!-- <div class="box-header"></div> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
