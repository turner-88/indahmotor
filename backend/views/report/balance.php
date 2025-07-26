 
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
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\Outgoing */

$this->title = $title;
$this->params['breadcrumbs'][] = 'Report';
$this->params['breadcrumbs'][] = $title;
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

    <div style="display:inline-block; width:200px; vertical-align:bottom">
    <?= Select2::widget([
        'name' => 'salesman_id',
        'value' => $salesman_id,
        'data' => ArrayHelper::map(Salesman::find()->all(), 'id', 'name'),
        'options' => ['placeholder' => 'semua salesman'],
        'pluginOptions' => ['allowClear' => true],
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
        'salesman_id'   => $salesman_id,
        'to_pdf'        => 1,
    ]) ?>" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print </a>


    &nbsp;&nbsp;&nbsp;
    <?= Yii::$app->user->can('/*') ? Html::a('<i class="fa fa-th-list"></i> History Saldo', ['balance-history-reverted', 'date_start' => $date_start, 'date_end' => $date_end], ['class' => 'btn btn-default']) : '' ?>
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

        <?php if (!$model) { ?>
            <span class="text-muted">Tidak ada data.</span>
        <?php } else { ?>

        <div class="row" style="display:none">
            <div class="col-md-2">
                <div class="form-panel text-center" style="margin-bottom:10px">
                    <b>Pembelian</b>
                    <br><big><big>&nbsp;<?= Yii::$app->formatter->asDecimal($model['total_incoming'], 0) ?>&nbsp;</big></big>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-panel text-center" style="margin-bottom:10px">
                    <b>Penjualan</b>
                    <br><big><big>&nbsp;<?= Yii::$app->formatter->asDecimal($model['total_outgoing'], 0) ?>&nbsp;</big></big>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-panel text-center" style="margin-bottom:10px">
                    <b>Setoran</b>
                    <br><big><big>&nbsp;<?= Yii::$app->formatter->asDecimal($model['total_amount'], 0) ?>&nbsp;</big></big>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-panel text-center" style="margin-bottom:10px">
                    <b>Return</b>
                    <br><big><big>&nbsp;<?= Yii::$app->formatter->asDecimal($model['total_return'], 0) ?>&nbsp;</big></big>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-panel text-center" style="margin-bottom:10px">
                    <b>Diskon Bayar</b>
                    <br><big><big>&nbsp;<?= Yii::$app->formatter->asDecimal($model['total_adjustment'], 0) ?>&nbsp;</big></big>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-panel text-center" style="margin-bottom:10px">
                    <b>Total Piutang</b>
                    <br><big><big>&nbsp;<?= Yii::$app->formatter->asDecimal($model['total_debt'], 0) ?>&nbsp;</big></big>
                </div>
            </div>
        </div>
               
        <div class="detail-view-container" style="margin-bottom:10px">
            <table class="table table-bordered" style="margin-bottom:0">
                <tr><th class="text-right" style="width:250px; white-space:nowrap; padding:5px;">Total Piutang</th><td style="padding:5px"><?= Yii::$app->formatter->asDecimal($model['total_debt'], 0) ?></td></tr>
            </table>
        </div>

        <div class="detail-view-container" style="margin-bottom:10px">
            <table class="table table-bordered" style="margin-bottom:0">
                <tr><th class="text-right" style="width:250px; white-space:nowrap; padding:5px;">Penjualan</th><td style="padding:5px"><?= Yii::$app->formatter->asDecimal($model['total_outgoing'], 0) ?></td></tr>
                <tr><th class="text-right" style="width:250px; white-space:nowrap; padding:5px;">Setoran</th><td style="padding:5px"><?= Yii::$app->formatter->asDecimal($model['total_amount'], 0) ?> <span class="pull-right text-muted"><b><?php if ($model['total_outgoing']) { echo Yii::$app->formatter->asDecimal($model['total_amount']/$model['total_outgoing'] * 100, 2) . ' %'; } ?></b></span></td></tr>
                <tr><th class="text-right" style="width:250px; white-space:nowrap; padding:5px;">Return</th><td style="padding:5px"><?= Yii::$app->formatter->asDecimal($model['total_return'], 0) ?></td></tr>
                <tr><th class="text-right" style="width:250px; white-space:nowrap; padding:5px;">Diskon Bayar</th><td style="padding:5px"><?= Yii::$app->formatter->asDecimal($model['total_adjustment'], 0) ?></td></tr>
            </table>
        </div>

        <?php if (!$salesman_id) { ?>
        <div class="detail-view-container" style="margin-bottom:10px">
            <table class="table table-bordered" style="margin-bottom:0">
                <tr><th class="text-right" style="width:250px; white-space:nowrap; padding:5px;">Pembelian</th><td style="padding:5px"><?= Yii::$app->formatter->asDecimal($model['total_incoming'], 0) ?></td></tr>
                <tr><th class="text-right" style="width:250px; white-space:nowrap; padding:5px;">Pengeluaran</th><td style="padding:5px"><?= Yii::$app->formatter->asDecimal($model['total_expense'], 0) ?></td></tr>
            </table>
        </div>
        <div class="detail-view-container" style="margin-bottom:10px">
            <table class="table table-bordered" style="margin-bottom:0">
                <tr><th class="text-right" style="width:250px; white-space:nowrap; padding:5px;">Selisih (Setoran - Pengeluaran)</th><td style="padding:5px"><?= Yii::$app->formatter->asDecimal($model['total_amount'] - $model['total_expense'], 0) ?></td></tr>
            </table>
        </div>
        <?php } ?>

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