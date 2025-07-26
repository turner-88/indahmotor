<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Incoming */

$this->title = $model->serial;
$this->params['breadcrumbs'][] = ['label' => 'Incoming', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="incoming-view box-- box-info--">

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
                'serial',
                'date',
                'due_date',
                'incomingType.name:text:Incoming Type',
                'supplier.name:text:Supplier',
                'storage.name:text:Storage',
                'customer.name:text:Customer',
                'returnPlan.name:text:Return Plan',
                'outgoingItem.name:text:Outgoing Item',
                'salesman.name:text:Salesman',
                'remark:ntext',
                [
                    'attribute' => 'total',
                    'format' => ['decimal', 2],
                ],
                'is_deleted:integer',
                // 'created_at:datetime',
                // 'updated_at:datetime',
                // 'createdBy.username:text:Created By',
                // 'updatedBy.username:text:Updated By',
            ],
        ]) ?>
        </div>
    </div>
</div>
