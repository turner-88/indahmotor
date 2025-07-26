<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\OutgoingItem;
use backend\helpers\ReportHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\Outgoing */

$this->title = 'Print All';
$this->params['breadcrumbs'][] = ['label' => 'Penjualan', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Rekap Faktur', 'url' => ['print-resume']];
$this->params['breadcrumbs'][] = 'Print';
?>

<?php if (!isset($to_pdf) || !$to_pdf) { ?>

<style>
    .kv-grid-container {border:none !important; overflow: hidden; margin-bottom: 5px; padding-bottom: 5px}
    .table-report {width:100%}
    .table-report td {padding:0px 5px !important}
</style>

<p>
    <button style="display:none" onclick="window.print()" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print</button>
    <a id="print" target="_blank" href="<?= Url::to(['/outgoing-sale/print',
        'id'        => $model->id,
        'to_pdf'    => 1,
    ]) ?>" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print </a>
</p>

<?php } ?>


<?php 
$pagebreak = 0;
foreach($models as $model) { 
    if ($pagebreak) echo '<pagebreak />'; 
    $pagebreak = 1;
?>

<div class="detail-view-container" style="padding:<?= isset($to_pdf) && $to_pdf ? '0px' : '20px' ?>">
    <div class="printable">

        <div class="header">
            <table class="table-report-header" width="100%">
                <tr>
                    <td width="40%"><big><b>IM <?= $model->idText ?></b> <br> <?= strtoupper(date('d F Y', strtotime($model->date))) ?></big></td>
                    <td width="20%" class="text-center"><big><big><b>FAKTUR</b></big></big></td>
                    <td width="40%" class="text-right"><big><b><?= $model->customer->name ?></b></big> <br> <?= $model->customer->address ?></td>
                </tr>
            </table>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <table width="100%" class="table-report">
                    <thead>
                    <tr>
                        <td width="">Box</td>
                        <td width="">Barang</td>
                        <td width="">Merk</td>
                        <td width="">Tipe</td>
                        <td width="">Qty</td>
                        <td width=""></td>
                        <td width="" align="right">Hrg</td>
                        <td width="" align="right">Jml</td>
                    </tr>
                    </thead>
                <?php 
                $i = 0;
                foreach($model->outgoingItems as $outgoingItem) { 
                    $i++;
                    if ($i % 20 == 1 && $i != 1) { 
                        echo '</table>';
                        echo '<pagebreak/>';
                        echo '
                        <div class="header">
                            <table class="table-report-header" width="100%">
                                <tr>
                                    <td width="40%"><big><b>IM ' . $model->idText . '</b> <br> ' . strtoupper(date('d F Y', strtotime($model->date))) . '</big></td>
                                    <td width="20%" class="text-center"><big><big><b>FAKTUR</b></big></big></td>
                                    <td width="40%" class="text-right"><big><b>' . $model->customer->name . '</b></big> <br> ' . $model->customer->address . '</td>
                                </tr>
                            </table>
                        </div>
                        ';
                        echo '<table width="100%" class="table-report">
                            <thead>
                            <tr>
                                <td width="">Box</td>
                                <td width="">Barang</td>
                                <td width="">Merk</td>
                                <td width="">Tipe</td>
                                <td width="">Qty</td>
                                <td width=""></td>
                                <td width="" align="right">Hrg</td>
                                <td width="" align="right">Jml</td>
                            </tr>
                            </thead>';
                    }
                ?>
                    <tr>
                        <td style="white-space:nowrap; overflow:hidden" width=""><?= $outgoingItem->box_number ?></td>
                        <td style="white-space:nowrap; overflow:hidden; min-width:40%; max-width:50%"><?= $outgoingItem->item->name ?></td>
                        <td style="white-space:nowrap; overflow:hidden" width=""><?= $outgoingItem->item->brand ?></td>
                        <td style="white-space:nowrap; overflow:hidden" width=""><?= $outgoingItem->item->type ?></td>
                        <td style="white-space:nowrap; overflow:hidden" width=""><?= Yii::$app->formatter->asDecimal($outgoingItem->quantity, 0) ?></td>
                        <td style="white-space:nowrap; overflow:hidden" width=""><?= $outgoingItem->item->unit_of_measurement ?></td>
                        <td style="white-space:nowrap; overflow:hidden" width="" align="right"><?= Yii::$app->formatter->asDecimal($outgoingItem->price, 0) ?></td>
                        <td style="white-space:nowrap; overflow:hidden" width="" align="right"><?= Yii::$app->formatter->asDecimal($outgoingItem->subtotal, 0) ?></td>
                    </tr>
                <?php } ?>
                </table>
            </div>
        </div>
        
        <?php 
        $max_row = $model->count_of_items % 20;
        if ($max_row) {
            for ($i = 20 - $max_row; $i >= 1; $i--) {
                echo '<br>&nbsp;';
            }
        }
        ?>

        <div class="footer">
            <table width="100%" class="table-report-footer">
                <tr>
                    <td style="padding:0" width="65%"><big><big><b>INDAH MOTOR</b></big></big></td>
                    <td style="padding:0" width="30%" class="text-center"><big><big><b><?= count($model->outgoingItems) ?></b> ITEM</big></big></td>
                    <td style="padding:0" width="35%" class="text-right"><big><big>TOTAL : Rp <b><?= Yii::$app->formatter->asDecimal($model->total, 0) ?></b></big></big></td>
                </tr>
                <tr>
                    <td style="padding:0" width="65%"><big><i>Selama belum lunas, barang ini dianggap barang titipan.</i></big></td>
                    <td style="padding:0" colspan="2" class="text-right"><big><?= $model->remark ?></big></td>
                </tr>
            </table>
        </div>

    </div>
</div>

<?php } ?>

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