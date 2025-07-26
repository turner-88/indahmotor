 
<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\daterange\DateRangePicker;
use backend\models\Customer;
use backend\models\Salesman;
use backend\models\OutgoingItem;
use backend\helpers\ReportHelper;
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

    <div style="display:inline-block; width:200px; vertical-align:bottom">
    <?= Select2::widget([
        'name' => 'customer_id',
        'value' => $customer_id,
        'data' => ArrayHelper::map(Customer::find()->all(), 'id', 'shortText'),
        'options' => ['placeholder' => 'semua customer'],
        'pluginOptions' => ['allowClear' => true],
    ]); ?>
    </div>

    <?php if (Yii::$app->user->can('owner')) { ?>
    <div style="display:inline-block; width:100px; vertical-align:bottom" class="form-control">
        <?= Html::checkbox('with_profit', $with_profit, ['label' => 'Laba']) ?>
    </div>
    <?php } ?>

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
        'customer_id'   => $customer_id,
        'with_profit'   => $with_profit,
        'to_pdf'        => 1,
    ]) ?>" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print </a>

    &nbsp;&nbsp;&nbsp;<a href="<?= Url::to(['/report/outgoing-item',
        'date_start'    => $date_start,
        'date_end'      => $date_end,
        'salesman_id'   => $salesman_id,
        'customer_id'   => $customer_id,
        'with_profit'   => $with_profit,
    ]) ?>" class="btn btn-default"><i class="fa fa-th-list"></i> Penjualan Item </a>

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
        
        
        
        <table width="100%" class="table table-report">
            <thead>
            <tr class="thead" style="border-bottom:2px solid #eee;">
                <td class="text-right" style="width:1px">No</td>
                <td>Faktur</td>                
                <?php if ($date_start != $date_end) { ?><td>Tanggal</td><?php } ?>
                <?php if (!$salesman_id) { ?><td>Salesman</td><?php } ?>
                <?php if (!$customer_id) { ?><td>Customer</td><?php } ?>
                <?php if ($with_profit && Yii::$app->user->can('owner')) { ?><td class="text-right">Modal</td><?php } ?>
                <td class="text-right">Penjualan</td>
                <?php if ($with_profit && Yii::$app->user->can('owner')) { ?><td class="text-right">Laba</td><?php } ?>
            </tr>
            </thead>

        <?php
        $i = 0;
        $total = 0;
        $total_modal = 0;
        $total_profit = 0;
        foreach ($models as $model) {
            // $modal = OutgoingItem::find()->joinWith(['item'])->where(['outgoing_id' => $model->id])->sum('purchase_net_price * outgoing_item.quantity');
            $modal = $model->modal;
            $profit = $model->total - $modal;
            $total += $model->total;
            $total_modal += $modal;
            $total_profit += $profit;
        ?>
            <tr>
                <td class="text-right" style="width:1px"><?= ++$i ?></td>
                <td><?= $model->idText ?></td>
                <?php if ($date_start != $date_end) { ?><td><?= Yii::$app->formatter->asDate($model->date) ?></td><?php } ?>
                <?php if (!$salesman_id) { ?><td><?= $model->salesman ? $model->salesman->name : '' ?></td><?php } ?>
                <?php if (!$customer_id) { ?><td><?= $model->customer ? $model->customer->name : '' ?></td><?php } ?>
                <?php if ($with_profit && Yii::$app->user->can('owner')) { ?><td class="text-right"><?= Yii::$app->formatter->asDecimal($modal, 0) ?></td><?php } ?>
                <td class="text-right"><?= Yii::$app->formatter->asDecimal($model->total, 0) ?></td>
                <?php if ($with_profit && Yii::$app->user->can('owner')) { ?><td class="text-right <?= $profit < 0 ? 'text-red' : '' ?>"><?= Yii::$app->formatter->asDecimal($profit, 0) ?></td><?php } ?>
            </tr>
        <?php } ?>
        </table>
        
        <?php // $total_profit += $total - $total_modal; ?>
        
        <table class="table table-report-footer">
            <?php if ($with_profit && Yii::$app->user->can('owner')) { ?>
            <tr>
                <td class="text-right"><b>TOTAL MODAL : Rp </b></td>
                <td class="text-right" style="width:1px; white-space:nowrap"><b><?= Yii::$app->formatter->asDecimal($total_modal, 0) ?></b></td>
            </tr>
            <?php } ?>
            <tr>
                <td class="text-right"><b>TOTAL PENJUALAN : Rp </b></td>
                <td class="text-right" style="width:1px; white-space:nowrap"><b><?= Yii::$app->formatter->asDecimal($total, 0) ?></b></td>
            </tr>
            <?php if ($with_profit && Yii::$app->user->can('owner')) { ?>
            <tr>
                <td class="text-right"><b>TOTAL LABA : Rp </b></td>
                <td class="text-right" style="width:1px; white-space:nowrap"><b><?= Yii::$app->formatter->asDecimal($total_profit, 0) ?></b></td>
            </tr>
            <?php } ?>
        </table>

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

<style>
    .text-red {
        color: red;
        font-weight: bold;
    }
</style>