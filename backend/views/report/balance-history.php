 
<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\daterange\DateRangePicker;
use backend\helpers\ReportHelper;
use backend\models\Salesman;
use backend\models\BalanceHistory;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\Outgoing */

$this->title = $title;
$this->params['breadcrumbs'][] = 'Report';
$this->params['breadcrumbs'][] = $title;

$from               = $date_start;
$to                 = $date_end;

$sum_in     = BalanceHistory::find()->andWhere(['<', 'date', $from])->sum('`in`');
$sum_out    = BalanceHistory::find()->andWhere(['<', 'date', $from])->sum('`out`');
$balance_before = $sum_in - $sum_out;

?>

<?php if (!$to_pdf) { ?>

<style>
    .kv-grid-container {border:none !important; overflow: hidden; margin-bottom: 5px; padding-bottom: 5px}
    .table-report {width:100%}
    .table-report td {padding:0px 5px !important}
    .table-report-footer td {padding:0px 5px !important; border:none !important}
</style>

    
    <?php $form = ActiveForm::begin(['action' => Url::to([$view]), 'method' => 'get', 'options' => ['style' => 'display:inline']]); ?>
    
    <?php /* echo DateRangePicker::widget([
        'name' => 'date_range',
        'value' => $date_start . ' - ' . $date_end,
        // 'useWithAddon'=>false,
        'presetDropdown' => true,
        'convertFormat' => true,
        'startAttribute' => 'date_start',
        'endAttribute' => 'date_end',
        'pluginOptions' => [
            'locale' => ['format' => 'd/m/Y'],
            'opens' => 'right',
        ],
        'options' => [
            'class' => 'form-control',
            'style' => 'display:inline-block !important; width:auto; vertical-align:middle',
        ]
    ]); */ ?>

    <div style="display: inline-block; width: 150px; vertical-align: bottom;">
    <?= DatePicker::widget([
        'name' => 'date_start',
        'value' => $date_start,
        'removeButton' => false,
        'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd'],
        'options' => [
            // 'class' => 'form-control',
            // 'style' => 'display:inline-block !important; width:auto; vertical-align:middle',
        ]
    ]); ?>
    </div>

    <div style="display: inline-block; width: 150px; vertical-align: bottom;">
    <?= DatePicker::widget([
        'name' => 'date_end',
        'value' => $date_end,
        'removeButton' => false,
        'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd'],
        'options' => [
            // 'class' => 'form-control',
            // 'style' => 'display:inline-block !important; width:auto; vertical-align:middle',
        ]
    ]); ?>
    </div>

    <?= Html::button('<i class="glyphicon glyphicon-refresh"></i> ' . Yii::t('app', 'Reload'), [
        'type' => 'submit',
        'class' => 'btn btn-default',
        // 'style' => 'border-top-left-radius:0; border-bottom-left-radius:0; margin-left:-1px',
    ]) ?>

    <?php ActiveForm::end(); ?>
    
    <button style="display:none" onclick="window.print()" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print</button>
    <a id="print" target="_blank" href="<?= Url::to([$view,
        'date_start'    => $date_start,
        'date_end'      => $date_end,
        'to_pdf'        => 1,
    ]) ?>" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print </a>

    &nbsp;&nbsp;&nbsp;<?= Html::a('<i class="fa fa-th"></i> Resume', ['balance', 'date_start' => $date_start, 'date_end' => $date_end], ['class' => 'btn btn-default pull-right']) ?>
    <?= Html::a('<i class="fa fa-th-list"></i> History Stock', ['stock-history-daily', 'date_start' => $date_start, 'date_end' => $date_end], ['class' => 'btn btn-default']) ?>

    <p></p>

    <style>
        .table-report > tbody tr > td { padding: 0px 5px; border-bottom: 1px solid #eee; }
        .table-report tr.thead td { font-weight: bold; text-transform: uppercase; border-top:none }
        thead {display: table-header-group;}
    </style>

<?php } ?>


<div class="detail-view-container" style="padding:<?= isset($to_pdf) && $to_pdf ? '0px' : '20px' ?>">
    <div class="printable">

        <?= !isset($to_pdf) || !$to_pdf ? ReportHelper::header($params) . '<br>' : '' ?>

        <?php if (!$models) { ?>
            <span class="text-muted">Tidak ada data.</span>
        <?php } else { ?>
            
            <div class="text-right"><b>SALDO AWAL : Rp <?= Yii::$app->formatter->asDecimal($balance_before, 0) ?></b></div>
            <p></p>
            
            <table width="100%" class="table table-report">
                <thead>
                <tr class="thead" style="border-bottom:2px solid #eee;">
                    <td class="text-right" style="width:1px">No</td>
                    <?php if ($date_start != $date_end) { ?><td>Tanggal</td><?php } ?>
                    <td class="">ID Ref</td>
                    <td class="">Uraian</td>
                    <td class="text-right">Setoran</td>  
                    <td class="text-right">Pengeluaran</td>
                    <td class="text-right">Saldo</td>
                </tr>
                </thead>

            <?php
            $i = 0;
            $total = 0;
            foreach ($models as $model) {
                if ($model->transaction_type == 'i') $balance_before += $model->in;
                if ($model->transaction_type == 'o') $balance_before -= $model->out;
            ?>
                <tr>
                    <td class="text-right" style="width:1px"><?= ++$i ?></td>
                    <?php if ($date_start != $date_end) { ?><td><?= Yii::$app->formatter->asDate($model->date) ?></td><?php } ?>
                    <td class=""><?= $model->transaction_id ?></td>
                    <td class=""><?= nl2br($model->remark) ?></td>
                    <td class="text-right"><?= $model->in ? Yii::$app->formatter->asDecimal($model->in, 0) : '' ?></td>
                    <td class="text-right"><?= $model->out ? Yii::$app->formatter->asDecimal($model->out, 0) : '' ?></td>
                    <td class="text-right"><?= Yii::$app->formatter->asDecimal($balance_before, 0) ?></td>
                </tr>
            <?php } ?>
            </table>

            <div class="text-right"><b>SISA SALDO : Rp <?= Yii::$app->formatter->asDecimal($balance_before, 0) ?></b></div>

        <?php } ?>
        
    </div>
</div>


<?php 
if (!isset($to_pdf) || !$to_pdf) {

$js = 
<<<JAVASCRIPT
jQuery(document).bind("keyup keydown", function(e){
if(e.ctrlKey && e.keyCode == 80){
    $('#print').get(0).click();
    return false;
}
});
JAVASCRIPT;

$this->registerJs($js, \yii\web\View::POS_READY);
}
?>