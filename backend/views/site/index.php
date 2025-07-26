<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\JsExpression;
use miloschuman\highcharts\Highcharts;
use backend\helpers\ReportHelper;
use backend\models\Log;
use backend\models\Incoming;
use backend\models\Outgoing;
use backend\models\Payment;
use backend\models\Expense;
use backend\models\Capital;
use backend\models\Order;

/* @var $this yii\web\View */

$this->title = 'indah motor';
$reports = Log::find()->limit(9)->all();

$year_options = [];
$yearStart = '2019';
for ($i = $yearStart; $i <= date('Y'); $i++) {
    $year_options = array_replace($year_options, [$i => $i]);
}
?>

<div class="site-index">
    <?php // echo Html::a('Backup Database', ['/database/backup'], ['class' => 'btn btn-default']) ?>
    
    <div class="pull-right">
        <?= Html::dropDownList('year', $year, $year_options, ['id' => 'year', 'class' => 'form-control', 'onchange' => "javascript:window.location='".Url::to(['site/index'])."&year='+this.value"]) ?>
    </div>


    <div id="chart">

    <br>
    <br>

    <?php 

    $date_start = date('01/01/'.$year);
    $date_end   = date('31/12/'.$year);

    $categories = [];
    $series     = [];
    $colors     = ['yellow', 'blue', 'red', 'green', 'purple'];
    $tables = [
        'incoming' => [
            'model' => 'Incoming',
            'label' => 'Pembelian',
            'color' => 'orange',
        ],
        'outgoing' => [
            'model' => 'Outgoing',
            'label' => 'Penjualan',
            'color' => 'blue',
        ],
        'payment_out' => [
            'model' => 'Payment-Out',
            'label' => 'Pembayaran',
            'color' => 'red',
        ],
        'payment_in' => [
            'model' => 'Payment-In',
            'label' => 'Penerimaan',
            'color' => 'green',
        ],
        'expense' => [
            'model' => 'Expense',
            'label' => 'Pengeluaran',
            'color' => 'purple',
        ],
    ];
    // krsort($tables);
    foreach ($tables as $table) {

        $data = [];
        $labels = [];
        $months = ReportHelper::months();
        foreach ($months as $key => $value) {
        
            // incoming
            $labels[] = $value;
            switch ($table['model']) {
                case 'Incoming' :
                    $total  = Incoming::find()->where(['month(`date`)' => $key, 'year(`date`)' => $year])->sum('total');
                    break;
            
                case 'Outgoing' :
                    $total  = Outgoing::find()->where(['month(`date`)' => $key, 'year(`date`)' => $year])->sum('total');
                    break;

                case 'Payment-Out' :
                    $total  = Payment::find()->where(['month(`date`)' => $key, 'year(`date`)' => $year])->andWhere(['is not', 'supplier_id', null])->sum('amount');
                    break;

                case 'Payment-In' :
                    $total  = Payment::find()->where(['month(`date`)' => $key, 'year(`date`)' => $year])->andWhere(['is not', 'customer_id', null])->sum('amount');
                    break;

                case 'Expense' :
                    $total  = Expense::find()->where(['month(`date`)' => $key, 'year(`date`)' => $year])->sum('amount');
                    break;
                        
                default: break;
            }
                   
            $intval = $total ? intval($total) : 0;
            // $data[] = $intval ? $intval : null;
            $data[] = [
                'y' => ($intval ? $intval : null), 
                /* 'url' => Url::to([
                    'table/resume-category',
                    'table_id' => $table->id,
                ]) */
            ];
        }


        $series[] = [
            'name' => $table['label'], 
            'data' => $data,
            'color' => $table['color'],
            'edgeColor' => 'white',
            'edgeWidth' => 0.1,
            'dataLabels' => [
                'enabled' => true,
                'format' => '{point.y:.0f}',
            ],
        ];
    }
    ?>

    <?= Highcharts::widget([
        'setupOptions' => [
            'lang' => [
                'numericSymbols' => [' rb', ' jt', ' M', ' T', ' P', ' E']
            ],
        ],
        'options' => [
            'credits' => ['enabled' => false],
            'tooltip' => ['enabled' => false],
            'chart' => [
                'backgroundColor' => null,
                'type' => 'column',
                'style' => [
                    'fontFamily' => 'Segoe UI, Helvetica, Arial, sans-serif',
                ],
            ],
            // 'title' => ['text' => 'Hasil Penilaian'],
            'title' => ['text' => ''],
            'xAxis' => [
                'categories' => $labels
            ],
            'yAxis' => [
                'title' => ['text' => ''],
                'min' => 0,
                // 'max' => 100,
                /* 'labels' => [
                    'formatter' => new JsExpression('function() { return this.value == 0 ? 0 : (this.value/1000000) + " jt"; }'),
                ], */
            ],
            'series' => $series,
            // 'legend' => false,
            'plotOptions' => [
                'series' => [
                    // 'cursor' => 'pointer',
                    /* 'point' => [
                        'events' => [
                            'click' => new JsExpression('function() { window.location=this.options.url; }')
                        ]
                    ], */
                ],
            ],
        ]
    ]); ?>
    </div>
</div>
    

<?php
$this->registerJs(' 
        initializeClock();

        function initializeClock() {

            function updateClock() {
                $(Highcharts.charts).each(function(i,chart){
                    chart.reflow(); 
                });
                $(window).resize();
                console.log("window resized");
                clearInterval(timeinterval);
            }

            updateClock();
            var timeinterval = setInterval(updateClock, 1000);
        }
        ', \yii\web\VIEW::POS_READY);
?>