<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\OutgoingItem;

/* @var $this yii\web\View */
/* @var $model backend\models\Outgoing */

$this->title = $model->idText.' - '.$model->customer->name;
$this->params['breadcrumbs'][] = ['label' => 'Penjualan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="outgoing-view box-- box-info--">

    <div class="box-body--">
        <p>
        <?= Html::a('<i class="glyphicon glyphicon-print"></i> ' . Yii::t('app', 'Print'), ['print', 'id' => $model->id, 'to_pdf' => 1], [
            'class' => 'btn btn-default',
            'target' => '_blank',
        ]) ?>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> '. 'Update', ['update-ajax', 'id' => $model->id], [
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
                'idText:text:ID',
                // 'serial',
                'date',
                'due_date',
                // 'outgoingType.name:text:Outgoing Type',
                'customer.name:text:Customer',
                'salesman.name:text:Salesman',
                'remark:ntext',
                [
                    'attribute' => 'total',
                    'format' => ['decimal', 0],
                ],
                // 'is_deleted:datetime',
                // 'created_at:datetime',
                // 'updated_at:datetime',
                'createdBy.username:text:Created By',
                // 'updatedBy.username:text:Updated By',
                [
                    'attribute' => 'total_payment',
                    'format' => ['decimal', 0],
                ],
                'paymentStatusHtml:html:Status Pembayaran',
            ],
        ]) ?>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <?php 
                $dataProvider = new \yii\data\ActiveDataProvider([
                    'query' => OutgoingItem::find()->where(['outgoing_id' => $model->id]),
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
                    'summary' => false,
                    'toolbar' => [
                        Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success']),
                        Html::a('<i class="fa fa-repeat"></i> ' . Yii::t('app', 'Reload'), ['index'], ['data-pjax' => 0, 'class' => 'btn btn-default']),
                        '{toggleData}',
                            // $exportMenu,
                    ],
                    'panel' => false,
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
                        [
                            'attribute' => 'box_number',
                            'headerOptions' => ['class' => 'text-right'],
                            'contentOptions' => ['class' => 'text-right'],
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
