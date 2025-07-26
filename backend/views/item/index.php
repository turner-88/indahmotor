<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use backend\models\PriceGroup;
use backend\models\ItemPrice;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Barang';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="item-index box-- box-primary-- box-body--">

<?php 
        $exportColumnsVIP = [
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

        $exportMenuVIP = ExportMenu::widget([
            'dataProvider' => $dataProviderVIP,
            'columns' => $exportColumnsVIP,
            'filename' => 'View Item Price',
            'fontAwesome' => true,
            'dropdownOptions' => [
                'label' => 'Export',
                'class' => 'btn btn-default'
            ],
            'target' => ExportMenu::TARGET_SELF,
            'exportConfig' => [
                // ExportMenu::FORMAT_CSV => false,
                // ExportMenu::FORMAT_EXCEL => false,
                // ExportMenu::FORMAT_HTML => false,
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
        ]); ?>

    <?php 
    $exportColumns = [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['class' => 'text-right serial-column'],
            'contentOptions' => ['class' => 'text-right serial-column'],
        ],
        'id',
        'shortcode',
        'name',
        'brand',
        'type',
        'unit_of_measurement',
        'current_quantity',
        'purchase_net_price',
        'purchase_gross_price',
        'purchase_discount',
        'location',
    ];

    foreach ($priceGroups = PriceGroup::find()->all() as $priceGroup) {
        $exportColumns = array_merge($exportColumns, [
            [
                'header' => $priceGroup->name . '-dsc ',
                'contentOptions' => ['class' => 'text-right'],
                'value' => function ($model) use ($priceGroup) {
                    $prices = ItemPrice::findOne(['item_id' => $model->id, 'price_group_id' => $priceGroup->id]);
                    $priceText = $prices ? $prices->discount : null;
                    return $priceText;
                }
            ],
            [
                'header' => $priceGroup->name . '-price ',
                'value' => function ($model) use ($priceGroup) {
                    $prices = ItemPrice::findOne(['item_id' => $model->id, 'price_group_id' => $priceGroup->id]);
                    $priceText = $prices ? $prices->price : null;
                    return $priceText;
                }
            ],
        ]);
    }

        $exportMenu = ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $exportColumns,
            'filename' => 'Item',
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
            ],
            // 'id',
            'shortcode',
            'name',
            'brand',
            'type',
            'unit_of_measurement',
            [
                'attribute' => 'current_quantity',
                'format' => ['decimal', 0],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            [
                'attribute' => 'purchase_net_price',
                'format' => ['decimal', 0],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            [
                'attribute' => 'purchase_gross_price',
                'format' => ['decimal', 0],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            [
                'attribute' => 'purchase_discount',
                'format' => ['decimal', 2],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            /* [
                'attribute' => 'prices',
                'format' => 'raw',
            ], */
            'location',
        ];

        foreach ($priceGroups = PriceGroup::find()->all() as $priceGroup) {
            $gridColumns = array_merge($gridColumns, [
                [
                    'header' => $priceGroup->name . '-dsc ',
                    'contentOptions' => ['class' => 'text-right'],
                    'value' => function($model) use ($priceGroup) {
                        $prices = ItemPrice::findOne(['item_id' => $model->id, 'price_group_id' => $priceGroup->id]);
                        $priceText = $prices ? Yii::$app->formatter->asDecimal($prices->discount, 0) : null;
                        return $priceText;
                    }
                ],
                [
                    'header' => $priceGroup->name . '-price ',
                    'contentOptions' => ['class' => 'text-right'],
                    'value' => function($model) use ($priceGroup) {
                        $prices = ItemPrice::findOne(['item_id' => $model->id, 'price_group_id' => $priceGroup->id]);
                        $priceText = $prices ? Yii::$app->formatter->asDecimal($prices->price, 0) : null;
                        return $priceText;
                    }
                ],
            ]);
        }
    ?>

    <?= GridView::widget([
        'id' => 'grid-id',
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'hover' => true,
        'striped' => false,
        'bordered' => false,
        'toolbar'=> [
            Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success']),
            Html::a('<i class="fa fa-repeat"></i> ' . 'Reload', ['index'], ['data-pjax'=>0, 'class'=>'btn btn-default']),
            Html::a('<i class="fa fa-th-list"></i> ' . 'Report', ['item-report', 'ready_stock_only' => 1], ['data-pjax'=>0, 'class'=>'btn btn-default']),
            '{toggleData}',
            // $exportMenu,
            $exportMenuVIP,
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