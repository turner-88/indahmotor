<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\PriceGroup */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Price Group', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="price-group-view box-- box-info--">

    <div class="box-body--">
        <p>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> '. 'Update', ['update', 'id' => $model->id], [
            'class' => 'btn btn-warning',
        ]) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> ' . 'Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        </p>

        <div class="detail-view-container">
        <?= DetailView::widget([
            'options' => ['class' => 'table detail-view'],
            'model' => $model,
            'attributes' => [
                // 'id',
                'name',
                [
                    'attribute' => 'default_discount',
                    'format' => ['decimal', 2],
                ],
                'description:ntext',
                // 'created_at:datetime',
                // 'updated_at:datetime',
                // 'createdBy.username:text:Created By',
                // 'updatedBy.username:text:Updated By',
            ],
        ]) ?>
        </div>
    </div>
</div>
