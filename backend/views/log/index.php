<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Logs';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="log-index box box-primary box-body">

    <?php 
        $exportColumns = [
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            'id',
            'level',
            'category',
            'log_time',
            'prefix',
            'message',
        ];

        $exportMenu = ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $exportColumns,
            'filename' => 'Logs',
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
            [
                'attribute' => 'level',
                'format' => 'integer',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            'category',
            [
                'attribute' => 'log_time',
                'format' => 'html',
                'contentOptions' => ['class' => 'text-mono'],
                'value' => function($model) 
                {   
                    $arr = explode('.', $model->log_time);
                    $arr[1] = isset($arr[1]) ? '.'.$arr[1] : '';
                    return str_replace(' ', '<br>', date('Y-m-d H:i:s'.$arr[1], $arr[0]));
                }
            ],
            [
                'attribute' => 'prefix',
                'format' => 'html',
                'contentOptions' => ['class' => 'text-mono'],
                'value' => function($model) 
                {
                    return str_replace(']', ']<br>', $model->prefix);
                }
            ],
            [
                'attribute' => 'message',
                'format' => 'ntext',
                'contentOptions' => ['class' => 'text-wrap'],
                'value' => function($model) {
                    return substr($model->message, 0, 200).' ...';
                }
             ],
        ];
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'pjax' => true,
        'hover' => true,
        'striped' => false,
        'bordered' => false,
        'toolbar'=> [
            Html::a(Yii::t('app', 'Clear Logs'), ['delete-all'], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete all log data?'),
                    'method' => 'post',
                ],
            ]),
            Html::a('<i class="fa fa-repeat"></i> ' . 'Reload', ['index'], ['data-pjax'=>0, 'class'=>'btn btn-default']),
            '{toggleData}',
            $exportMenu,
        ],
        'panel' => [
            'type' => 'no-border',
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