<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Config */

$this->title = $model->key;
$this->params['breadcrumbs'][] = ['label' => 'Config', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->key, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="config-update box-- box-warning--">

    <!-- <div class="box-header"></div> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
