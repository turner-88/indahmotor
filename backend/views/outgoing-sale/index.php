<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use kartik\widgets\Select2;
use kartik\widgets\DatePicker;
use backend\models\OutgoingType;
use backend\models\Customer;
use backend\models\Storage;
use backend\models\Supplier;
use backend\models\ReturnPlan;
use backend\models\IncomingItem;
use backend\models\Outgoing;
use backend\models\Salesman;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OutgoingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Penjualan';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="outgoing-index box-- box-primary-- box-body--">

    <?php 
        $exportColumns = [
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            'id',
            'serial',
            'date:date',
            'due_date:date',
            'outgoingType.name:text:Outgoing type',
            'customer.name:text:Customer',
            'storage.name:text:Storage',
            'supplier.name:text:Supplier',
            'returnPlan.name:text:Return plan',
            'incomingItem.item.name:text:Incoming item',
            'salesman.name:text:Salesman',
            'remark',
            'total',
            'is_deleted:integer',
            'created_at:datetime',
            'updated_at:datetime',
            'createdBy.username:text:Created By',
            'updatedBy.username:text:Updated By',
        ];

        $exportMenu = ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $exportColumns,
            'filename' => 'Outgoing',
            'fontAwesome' => true,
            'dropdownOptions' => [
                'label' => 'Export',
                'class' => 'btn btn-default'
            ],
            'target' => ExportMenu::TARGET_SELF,
            'exportConfig' => [
                ExportMenu::FORMAT_CSV => false,
                ExportMenu::FORMAT_EXCEL => false,
                ExportMenu::FORMAT_HTML => false,
            ],
            'styleOptions' => [
                ExportMenu::FORMAT_EXCEL_X => [
                    'font' => [
                        'color' => ['argb' => '00000000'],
                    ],
                    'fill' => [
                        // 'type' => PHPExcel_Style_Fill::FILL_NONE,
                        'color' => ['argb' => 'DDDDDDDD'],
                    ],
                ],
            ],
            'pjaxContainerId' => 'grid',
        ]);

        $gridColumns = [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'text-right serial-column'],
                'contentOptions' => ['class' => 'text-right serial-column'],
            ],
            [
                'contentOptions' => ['class' => 'action-column nowrap text-left'],
                'class' => 'yii\grid\ActionColumn',
                'template' => '{print} {view} {update-ajax} {delete}',
                'buttons' => [
                    'print' => function ($url, $model) {
                        return Html::a('', ['print', 'id' => $model->id, 'to_pdf' => 1], ['class' => 'glyphicon glyphicon-print btn btn-xs btn-default btn-text-default', 'target' => '_blank']);
                    },
                    'view' => function ($url) {
                        return Html::a('', $url, ['class' => 'glyphicon glyphicon-eye-open btn btn-xs btn-default btn-text-info']);
                    },
                    'update-ajax' => function ($url) {
                        return Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil btn btn-xs btn-default btn-text-warning']);
                    },
                    'delete' => function ($url) {
                        return Html::a('', $url, [
                            'class' => 'glyphicon glyphicon-trash btn btn-xs btn-default btn-text-danger', 
                            'data-method' => 'post', 
                            'data-confirm' => 'Are you sure you want to delete this item?']);
                    },
                ],
            ],
            /* [
                'attribute' => '',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a('<i class="fa fa-edit"></i>', ['update-ajax', 'id' => $model->id], ['class' => 'btn btn-xs']);
                },
            ], */
            [
                'attribute' => 'id',
                'value' => 'idText',
            ],
            // 'serial',
            [
                'attribute' => 'date',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE,
                'filterInputOptions' => ['placeholder' => ''],
                'filterWidgetOptions' => [
                    'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd'],
                ],
            ],
            [
                'attribute' => 'due_date',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE,
                'filterInputOptions' => ['placeholder' => ''],
                'filterWidgetOptions' => [
                    'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd'],
                ],
            ],
            [
                'attribute' => 'customer_id',
                'value' => 'customer.name',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Customer::find()->orderBy('name')->asArray()->all(), 'id', 'name'), 
                'filterInputOptions'=>['placeholder'=>''],
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear'=>true],
                ],
            ],
            [
                'attribute' => 'salesman_id',
                'value' => 'salesman.name',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Salesman::find()->orderBy('name')->asArray()->all(), 'id', 'name'), 
                'filterInputOptions'=>['placeholder'=>''],
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear'=>true],
                ],
            ],
            [
                'attribute' => 'total',
                'format' => ['decimal', 0],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            [
                'attribute' => 'count_of_items',
                'format' => ['decimal', 0],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            [
                'attribute' => 'remark',
                'format' => 'ntext',
                'contentOptions' => ['class' => 'text-wrap'],
            ],
            [
                'attribute'           => 'payment_status',
                'value'               => 'paymentStatusHtml',
                'format'              => 'html',
                'filterType'          => GridView::FILTER_SELECT2,
                'filter'              => Outgoing::paymentStatuses(),
                'filterInputOptions'  => ['placeholder' => ''],
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
            ],
            // 'is_deleted:integer',
            // 'created_at:integer',
            // 'updated_at:integer',
            // 'created_by:integer',
            // 'updated_by:integer',
        ];
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'pjax' => true,
        'hover' => true,
        'striped' => false,
        'bordered' => false,
        'toolbar'=> [
            Html::a('<i class="fa fa-plus"></i> ' . 'Penjualan Baru', ['create'], ['class' => 'btn btn-success']),
            Html::a('<i class="fa fa-repeat"></i> ' . 'Reload', ['index'], ['data-pjax'=>0, 'class'=>'btn btn-default']),
            // '{toggleData}',
            // $exportMenu,
            Html::a('<i class="fa fa-th-list"></i> ' . 'Lihat Detail', ['/outgoing-item/index'], ['data-pjax'=>0, 'class'=>'btn btn-default']),
            Html::a('<i class="fa fa-print"></i> ' . 'Rekap Faktur', ['print-resume'], ['data-pjax'=>0, 'class'=>'btn btn-default']),
            Html::a('<i class="fa fa-target"></i> ' . 'Set Status Pembayaran', ['set-payment-statuses'], ['data-pjax'=>0, 'class'=>'btn btn-default']),
        ],
        'panel' => [
            'type' => 'no-border transparent',
            'heading' => false,
            'before' => '{summary}',
            'after' => false,
        ],
        'panelBeforeTemplate' => '
            <div class="row">
                <div class="col-sm-8">
                    <div class="btn-toolbar kv-grid-toolbar" role="toolbar">
                        {toolbar}
                    </div> 
                </div>
                <div class="col-sm-4">
                    <div class="pull-right">
                        {before}
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        ',
        'pjaxSettings' => ['options' => ['id' => 'grid']],
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
    ]); ?>

</div>