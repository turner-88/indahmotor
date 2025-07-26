<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\IncomingItem */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Incoming Item', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="incoming-item-view box-- box-info--">

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
                'incoming.name:text:Incoming',
                'item.name:text:Item',
                [
                    'attribute' => 'quantity',
                    'format' => ['decimal', 2],
                ],
                [
                    'attribute' => 'price',
                    'format' => ['decimal', 2],
                ],
                [
                    'attribute' => 'subtotal',
                    'format' => ['decimal', 2],
                ],
                [
                    'attribute' => 'discount',
                    'format' => ['decimal', 2],
                ],
                [
                    'attribute' => 'gross_price',
                    'format' => ['decimal', 2],
                ],
                // 'created_at:datetime',
                // 'updated_at:datetime',
                // 'createdBy.username:text:Created By',
                // 'updatedBy.username:text:Updated By',
            ],
        ]) ?>
        </div>
    </div>
</div>
