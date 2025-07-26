 
<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\daterange\DateRangePicker;
use backend\helpers\ReportHelper;
use backend\models\Item;
use backend\models\IncomingItem;
use backend\models\OutgoingItem;
use backend\models\StockHistory;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\Outgoing */

$this->title = $title . ': ' . $item->name;
$this->params['breadcrumbs'][] = ['label' => 'Barang', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $item->name, 'url' => ['view', 'id' => $item->id]];
$this->params['breadcrumbs'][] = $title;

$from = $date_start;
$to   = $date_end;

$sum_incoming = IncomingItem::find()->joinWith(['incoming'])->where(['item_id' => $item_id])->andWhere(['<', 'date', $from])->sum('quantity');
$sum_outgoing = OutgoingItem::find()->joinWith(['outgoing'])->where(['item_id' => $item_id])->andWhere(['<', 'date', $from])->sum('quantity');
$stock_before = $sum_incoming - $sum_outgoing;

$sum_incoming_all = IncomingItem::find()->joinWith(['incoming'])->where(['item_id' => $item_id])->sum('quantity');
$sum_outgoing_all = OutgoingItem::find()->joinWith(['outgoing'])->where(['item_id' => $item_id])->sum('quantity');
$stock_before+= $item->current_quantity - ($sum_incoming_all - $sum_outgoing_all);
?>

<?php if (!$to_pdf) { ?>

    <style>
        .kv-grid-container {border:none !important; overflow: hidden; margin-bottom: 5px; padding-bottom: 5px}
        .table-report {width:100%}
        .table-report td {padding:0px 5px !important}
        .table-report-footer td {padding:0px 5px !important; border:none !important}
        .table-report .thead td {vertical-align: bottom !important;}
    </style>

    
    <?php $form = ActiveForm::begin(['action' => Url::to([$view]), 'method' => 'get', 'options' => ['style' => 'display:inline']]); ?>

    <input type="hidden" name="item_id" value="<?= $item_id ?>">
    
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
        'item_id'       => $item_id,
        'to_pdf'        => 1,
    ]) ?>" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print </a>

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

        <table class="stock-history">
            <tr>
                <th>Kode</th>
                <th>:</th>
                <td><?= $item->shortcode ?></td>
            </tr>
            <tr>
                <th>Nama</th>
                <th>:</th>
                <td><?= $item->name ?></td>
            </tr>
            <tr>
                <th>Merk</th>
                <th>:</th>
                <td><?= $item->brand ?></td>
            </tr>
            <tr>
                <th>Tipe</th>
                <th>:</th>
                <td><?= $item->type ?></td>
            </tr>
            <tr>
                <th>Stok</th>
                <th>:</th>
                <td><?= Yii::$app->formatter->asDecimal($item->current_quantity, 0).' '.$item->unit_of_measurement ?></td>
            </tr>
            <tr>
                <th>Lokasi</th>
                <th>:</th>
                <td><?= $item->location ?></td>
            </tr>
        </table>

        <?php if (!$models) { ?>
            <span class="text-muted">Tidak ada data.</span>
        <?php } else { ?>
        
        <div class="text-right"><b>STOK AWAL : <?= Yii::$app->formatter->asDecimal($stock_before, 0) ?></b></div>
        <p></p>
        
        <table width="100%" class="table table-report table-hover" style="border-top: 2px solid #ddd;">
            <thead>
            <tr class="thead" style="border-bottom:2px solid #ddd;">
                <td rowspan="2" class="text-right" style="width:1px">No</td>
                <?php if ($date_start != $date_end) { ?><td rowspan="2">Tanggal</td><?php } ?>
                <?php if (!$item_id) { ?><td rowspan="2">Item</td><?php } ?>
                <td colspan="3" class="text-center" style="border-left:1px solid #eee; border-right:1px solid #ddd;">Beli</td>
                <td colspan="3" class="text-center" style="border-left:1px solid #ddd; border-right:1px solid #ddd;">Jual</td>
                <td rowspan="2" class="text-right">Stok</td>
                <td rowspan="2" class="text-right"></td>
            </tr>
            <tr class="thead" style="border-bottom:2px solid #ddd;">
                <td class="text-right" style="border-left:1px solid #ddd;">Qty</td>
                <td class="text-right">Faktur</td>
                <td class="" style="border-right:1px solid #ddd;">Distributor</td>
                <td class="text-right" style="border-left:1px solid #ddd;">Qty</td>
                <td class="text-right">Faktur</td>
                <td class="" style="border-right:1px solid #ddd;">Pelanggan</td>
            </tr>
            </thead>

        <?php
        $i = 0;
        $total = 0;
        foreach ($models as $model) {
            if ($model->transaction_type == 'i') $stock_before += $model->quantity;
            if ($model->transaction_type == 'o') $stock_before -= $model->quantity;
        ?>
            <tr>
                <td class="text-right" style="width:1px"><?= ++$i ?></td>
                <?php if ($date_start != $date_end) { ?><td><?= Yii::$app->formatter->asDate($model->date) ?></td><?php } ?>
                <?php if (!$item_id) { ?><td><?= $model->item->name ?></td><?php } ?>
                <td class="text-right" style="border-left:1px solid #ddd;"><b><?= $model->transaction_type == 'i' ? Yii::$app->formatter->asDecimal($model->quantity, 0) : '' ?></b></td>
                <td class="text-right"><?= $model->transaction_type == 'i' ? str_pad($model->transaction_id, 4, '0', STR_PAD_LEFT) : '' ?></td>
                <td class="" style="border-right:1px solid #ddd;"><?= $model->supplier ?></td>
                <td class="text-right" style="border-left:1px solid #ddd;"><b><?= $model->transaction_type == 'o' ? Yii::$app->formatter->asDecimal($model->quantity, 0) : '' ?></b></td>
                <td class="text-right"><?= $model->transaction_type == 'o' ? str_pad($model->transaction_id, 4, '0', STR_PAD_LEFT) : '' ?></td>
                <td class="" style="border-right:1px solid #ddd;"><?= $model->customer ?></td>
                <td class="text-right"><?= Yii::$app->formatter->asDecimal($stock_before, 0) ?></td>
                <td width="1px"><?= $item->unit_of_measurement ?></td>
            </tr>
        <?php } ?>
        </table>

        <div class="text-right"><b>SISA STOK : <?= Yii::$app->formatter->asDecimal($stock_before, 0) ?></b></div>

        <?php } ?>
        
    </div>
</div>


<?php 
/* if (!isset($to_pdf) || !$to_pdf) {

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
} */
?>
