<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use kartik\widgets\Select2;
use kartik\widgets\DatePicker;
use backend\models\Incoming;
use backend\models\Supplier;
use backend\models\Outgoing;
use backend\models\Customer;
use backend\models\Payment;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Pembayaran');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php 
    $count_abnormal = Payment::find()->where([
        'and',
        ['is not', 'supplier_id', null],
        ['is not', 'customer_id', null],
    ])->count();
?>
<?php if ($count_abnormal) { ?>
    <div class="text-danger box box-danger box-body">
        <b><?= $count_abnormal ?></b> data abnormal perlu diperbaiki karena supplier dan customer sama-sama terisi!
        <p></p><?= Html::a('<i class="fa fa-search"></i>&nbsp; Filter data abnormal saja', ['index', 'abnormal' => 1], ['class' => 'btn btn-sm btn-default text-danger']) ?>
    </div>
<?php } ?>

<div class="payment-index box-- box-primary-- box-body--">

    <?php 
        $exportColumns = [
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            'id',
            'incoming.id:text:Faktur Pembelian',
            'supplier.name:text:Supplier',
            'outgoing.id:text:Faktur Penjualan',
            'customer.name:text:Customer',
            'date:date',
            'amount:integer',
            'adjustment',
            'return:integer',
            'remark',
            'created_at:datetime',
            'updated_at:datetime',
            'createdBy.username:text:Created By',
            'updatedBy.username:text:Updated By',
        ];

        $exportMenu = ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $exportColumns,
            'filename' => 'Payment',
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
                            'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?')]);
                    },
                ],
            ],
            [
                'header' => '',
                'format' => 'html',
                'value' => function($model) {
                    return Html::a('<i class="fa fa-edit"></i>', ['update-all', 'id' => $model->id]);
                },
            ],
            [
                'attribute' => 'id',
                'headerOptions' => ['class' => 'text-right serial-column'],
                'contentOptions' => ['class' => 'text-right serial-column'],
            ],
            [
                'attribute' => 'incoming_id',
                'value' => 'incoming.shortText',
                // 'filterType' => GridView::FILTER_SELECT2,
                // 'filter' => ArrayHelper::map(Incoming::find()->orderBy('id')->asArray()->all(), 'id', 'id'), 
                // 'filterInputOptions'=>['placeholder'=>''],
                // 'filterWidgetOptions' => [
                //     'pluginOptions' => ['allowClear'=>true],
                // ],
            ],
            [
                'attribute' => 'supplier_id',
                'value' => 'supplier.name',
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Supplier::find()->orderBy('name')->asArray()->all(), 'id', 'name'), 
                'filterInputOptions'=>['placeholder'=>''],
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear'=>true],
                ],
            ],
            [
                'attribute' => 'outgoing_id',
                'value' => 'outgoing.idText',
                // 'filterType' => GridView::FILTER_SELECT2,
                // 'filter' => ArrayHelper::map(Outgoing::find()->orderBy('id')->asArray()->all(), 'id', 'id'), 
                // 'filterInputOptions'=>['placeholder'=>''],
                // 'filterWidgetOptions' => [
                //     'pluginOptions' => ['allowClear'=>true],
                // ],
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
                'attribute' => 'date',
                'format' => 'date',
                'filterType' => GridView::FILTER_DATE,
                'filterInputOptions' => ['placeholder' => ''],
                'filterWidgetOptions' => [
                    'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd'],
                ],
            ],
            [
                'attribute' => 'amount',
                'format' => 'integer',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            [
                'attribute' => 'adjustment',
                'format' => 'integer',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            [
                'attribute' => 'return',
                'format' => 'integer',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            [
                'attribute' => 'remark',
                'format' => 'ntext',
                'contentOptions' => ['class' => 'text-wrap'],
            ],
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
            Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Pembayaran ke Supplier'), ['create-to-supplier'], ['class' => 'btn btn-success']),
            Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Pembayaran dari Customer'), ['create-from-customer'], ['class' => 'btn btn-success']),
            Html::a('<i class="fa fa-repeat"></i> ' . Yii::t('app', 'Reload'), ['index'], ['data-pjax'=>0, 'class'=>'btn btn-default']),
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
        'pjaxSettings' => ['options' => ['id' => 'grid']],
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
    ]); ?>

</div>