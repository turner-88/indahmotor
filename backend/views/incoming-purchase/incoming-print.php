 
<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\daterange\DateRangePicker;
use backend\models\Supplier;
use backend\helpers\ReportHelper;
use backend\models\Item;

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
    
    <style>
        .table-report > tbody tr > td { padding: 0px 5px; border-bottom: 1px solid #eee; }
        .table-report tr.thead td { font-weight: bold; text-transform: uppercase; border-top:none }
        thead {display: table-header-group;}
    </style>

<?php } ?>


<div class="detail-view-container" style="padding:<?= isset($to_pdf) && $to_pdf ? '0px' : '20px' ?>">
    <div class="printable">

        <?php $params['model'] = $model; ?>
        <?= !isset($to_pdf) || !$to_pdf ? ReportHelper::header($params) . '<br>' : '' ?>

        <?php if (!$model) { ?>
            <span class="text-muted">Tidak ada data.</span>
        <?php } else { ?>
        
        <table width="100%" class="table table-report">
            <thead>
            <tr class="thead" style="border-bottom:2px solid #eee;">
                <td class="text-right" style="width:1px">No</td>
                <td>Kode</td>
                <td>Item</td>
                <td>Merk</td>
                <td>Type</td>
                <td class="text-right">Qty</td>
                <td class="text-right"></td>
                <td class="text-right">Gross</td>
                <td class="text-right">Disc</td>
                <td class="text-right">Net</td>
                <td class="text-right">Subtotal</td>  
            </tr>
            </thead>

        <?php
        $i = 0;
        $total = 0;
        foreach ($model->incomingItemsAlphabetical as $model) {
            $total += $model->subtotal;
        ?>
            <tr>
                <td class="text-right" style="width:1px"><?= ++$i ?></td>
                <td><?= $model->item->shortcode ?></td>
                <td><?= $model->item->name ?></td>
                <td><?= $model->item->brand ?></td>
                <td><?= $model->item->type ?></td>
                <td class="text-right"><?= Yii::$app->formatter->asDecimal($model->quantity, 0) ?></td>
                <td class=""><?= $model->item->unit_of_measurement ?></td>
                <td class="text-right"><?= Yii::$app->formatter->asDecimal($model->gross_price, 0) ?></td>
                <td class="text-right"><?= Yii::$app->formatter->asDecimal($model->discount, 0) ?>%</td>
                <td class="text-right"><?= Yii::$app->formatter->asDecimal($model->price, 0) ?></td>
                <td class="text-right"><?= Yii::$app->formatter->asDecimal($model->subtotal, 0) ?></td>
            </tr>
        <?php } ?>
        </table>

        <div class="text-right"><b>TOTAL : <?= Yii::$app->formatter->asDecimal($total, 0) ?></b></div>

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