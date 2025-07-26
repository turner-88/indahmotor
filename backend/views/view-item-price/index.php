<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ViewItemPriceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'View Item Price';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="view-item-price-index box-- box-primary-- box-body--">

    <?php 
        $exportColumns = [
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            'id',
            'nama_barang',
            'kode_barang',
            'merk',
            'tipe',
            'satuan',
            'stok',
            'lokasi_penyimpanan',
            'harga_list:integer',
            'diskon_pembelian',
            'harga_net:integer',
            'diskon_A',
            'harga_A',
            'diskon_B',
            'harga_B',
            'diskon_C',
            'harga_C',
        ];

        $exportMenu = ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $exportColumns,
            'filename' => 'View Item Price',
            'fontAwesome' => true,
            'dropdownOptions' => [
                'label' => 'Export',
                'class' => 'btn btn-default'
            ],
            'target' => ExportMenu::TARGET_SELF,
            'exportConfig' => [
                // ExportMenu::FORMAT_CSV => false,
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
            'nama_barang',
            'kode_barang',
            'merk',
            'tipe',
            'satuan',
            [
                'attribute' => 'stok',
                'format' => ['decimal', 2],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            'lokasi_penyimpanan',
            'harga_list:integer',
            [
                'attribute' => 'diskon_pembelian',
                'format' => ['decimal', 2],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            'harga_net:integer',
            [
                'attribute' => 'diskon_A',
                'format' => ['decimal', 2],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            'harga_A',
            [
                'attribute' => 'diskon_B',
                'format' => ['decimal', 2],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            'harga_B',
            [
                'attribute' => 'diskon_C',
                'format' => ['decimal', 2],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            'harga_C',
        ];
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'pjax' => true,
        'hover' => true,
        'striped' => false,
        'bordered' => false,
        'toolbar'=> [
            Html::a('<i class="fa fa-plus"></i> ' . 'Create', ['create'], ['class' => 'btn btn-success']),
            Html::a('<i class="fa fa-repeat"></i> ' . 'Reload', ['index'], ['data-pjax'=>0, 'class'=>'btn btn-default']),
            '{toggleData}',
            $exportMenu,
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