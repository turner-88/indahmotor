<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use kartik\widgets\Select2;
use backend\models\Incoming;
use backend\models\Item;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\IncomingItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pembelian - Item';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="incoming-item-index box-- box-primary-- box-body--">

    <?php 
        $exportColumns = [
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            'id',
            'incoming.id:text:Incoming',
            'item.name:text:Item',
            'quantity',
            'price',
            'subtotal',
            'discount',
            'gross_price',
            'created_at:datetime',
            'updated_at:datetime',
            'createdBy.username:text:Created By',
            'updatedBy.username:text:Updated By',
        ];

        $exportMenu = ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $exportColumns,
            'filename' => 'Incoming Item',
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
            /* [
                'contentOptions' => ['class' => 'action-column nowrap text-left'],
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => function ($url) {
                        return Html::a('', $url, ['class' => 'glyphicon glyphicon-eye-open btn btn-xs btn-default btn-text-info']);
                    },
                    'update' => function ($url) {
                        return Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil btn btn-xs btn-default btn-text-warning']);
                    },
                    'delete' => function ($url) {
                        return Html::a('', $url, [
                            'class' => 'glyphicon glyphicon-trash btn btn-xs btn-default btn-text-danger', 
                            'data-method' => 'post', 
                            'data-confirm' => 'Are you sure you want to delete this item?']);
                    },
                ],
            ], */
            // 'id',
            [
                'attribute' => 'incoming_id',
                'value' => 'incoming.id',
                'label' => 'Faktur Pembelian',
                /* 'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Incoming::find()->orderBy('id')->asArray()->all(), 'id', 'id'), 
                'filterInputOptions'=>['placeholder'=>''],
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear'=>true],
                ], */
            ],
            [
                'attribute' => 'incoming_serial',
                'value' => 'incoming.serial',
                'label' => 'Faktur Distributor',
            ],
            [
                'attribute' => 'incoming_date',
                'value' => 'incoming.date',
                'label' => 'Tanggal',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE,
                'filterInputOptions' => ['placeholder' => ''],
                'filterWidgetOptions' => [
                    'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd'],
                ],
            ],
            [
                'attribute' => 'supplier_name',
                'value' => 'incoming.supplier.name',
                'label' => 'Distributor',
            ],
            [
                'attribute' => 'item_shortcode',
                'value' => 'item.shortcode',
                'label' => 'Kode',
            ],
            [
                'attribute' => 'item_name',
                'value' => 'item.name',
                'label' => 'Nama Barang',
                /* 'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Item::find()->orderBy('name')->asArray()->all(), 'id', 'name'), 
                'filterInputOptions'=>['placeholder'=>''],
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear'=>true],
                ], */
            ],
            [
                'attribute' => 'item_brand',
                'value' => 'item.brand',
                'label' => 'Merk',
            ],
            [
                'attribute' => 'item_type',
                'value' => 'item.type',
                'label' => 'Type',
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
                'attribute' => 'discount',
                'format' => ['decimal', 0],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            [
                'attribute' => 'gross_price',
                'format' => ['decimal', 0],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            // 'created_at:integer',
            // 'updated_at:integer',
            // 'created_by:integer',
            // 'updated_by:integer',
        ];
    ?>

    <?= GridView::widget([
        'id' => 'grid-id',
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'hover' => true,
        'striped' => false,
        'bordered' => false,
        'toolbar'=> [
            // Html::a('<i class="fa fa-plus"></i> ' . 'Create', ['create'], ['class' => 'btn btn-success']),
            Html::a('<i class="fa fa-chevron-left"></i> ' . 'Back', ['/incoming-purchase/index'], ['data-pjax'=>0, 'class'=>'btn btn-default']),
            Html::a('<i class="fa fa-repeat"></i> ' . 'Reload', ['index'], ['data-pjax'=>0, 'class'=>'btn btn-default']),
            // '{toggleData}',
            // $exportMenu,
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
        'pjaxSettings' => ['options' => ['id' => 'grid-pjax']],
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
    ]); ?>

</div>