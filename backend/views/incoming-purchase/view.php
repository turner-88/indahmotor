<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use backend\models\Item;
use backend\models\IncomingItem;

/* @var $this yii\web\View */
/* @var $model backend\models\Incoming */

$this->title = $model->id.' - '.$model->supplier->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pembelian'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="incoming-view">
    
    <p>
        <?= Html::a('<i class="glyphicon glyphicon-print"></i> ' . Yii::t('app', 'Print'), ['print', 'id' => $model->id, 'to_pdf' => 1], [
            'class' => 'btn btn-default',
            'target' => '_blank',
        ]) ?>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('app', 'Update'), ['update-ajax', 'id' => $model->id], [
            'class' => 'btn btn-warning',
        ]) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <i class="small pull-right text-muted">
            Created at <?= Yii::$app->formatter->asDatetime($model->created_at) ?> by <?= $model->createdBy->username ?>.
        </i>
    </p>

    <div class="detail-view-container">
        <?= DetailView::widget([
            'options' => ['class' => 'table detail-view'],
            'model' => $model,
            'attributes' => [
                // 'id',
                'serial',
                [
                    'attribute' => 'supplier_id',
                    'value' => $model->supplier->name . ' &nbsp; <small class="text-muted">' . $model->supplier->address . '</small>',
                    'format' => 'raw',
                ],
                'total:integer',
                'date:date',
                'due_date:date',
                'remark:ntext',
                'createdBy.username:text:Created By',
                /*[
                    'label' => 'Created',
                    'value' => 'at ' . Yii::$app->formatter->asDatetime($model->created_at) . ' by ' . $model->createdBy->username,
                ],
                [
                    'label' => 'Updated',
                    'value' => 'at ' . Yii::$app->formatter->asDatetime($model->updated_at) . ' by ' . $model->updatedBy->username,
                ],*/
                [
                    'attribute' => 'total_payment',
                    'format' => ['decimal', 0],
                ],
                'paymentStatusHtml:html:Status Pembayaran',
            ],
        ]) ?>
    </div>

        
        <?php 
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => IncomingItem::find()->joinWith(['item'])->where(['incoming_id' => $model->id])->orderBy('name'),
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        echo \kartik\grid\GridView::widget([
            'dataProvider' => $dataProvider,
                // 'pjax' => true,
            'hover' => true,
            'striped' => false,
            'bordered' => false,
            'panel' => false,
            'summary' => false,
            'pjaxSettings' => ['options' => ['id' => 'grid']],
                // 'filterModel' => $searchModel,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                ],
                [
                    'header' => 'Shortcode',
                    'value' => 'item.shortcode',
                ],
                [
                    'attribute' => 'item_id',
                    'value' => 'item.name',
                ],
                [
                    'header' => 'Brand',
                    'value' => 'item.brand',
                ],
                [
                    'header' => 'Type',
                    'value' => 'item.type',
                ],
                [
                    'header' => 'Base Unit',
                    'value' => 'item.unit_of_measurement',
                ],
                [
                    'attribute' => 'quantity',
                    'format' => ['decimal', 0],
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                ],
                [
                    'attribute' => 'price',
                    'format' => ['decimal', 0],
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                ],
                [
                    'attribute' => 'subtotal',
                    'format' => ['decimal', 0],
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                ],
                    // 'is_deleted:integer',
                    // 'created_at:integer',
                    // 'updated_at:integer',
                    // 'created_by:integer',
                    // 'updated_by:integer',
            ],
        ]);
        ?>
        
</div>