<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\OutgoingItem;
use backend\helpers\ReportHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\Outgoing */

$this->title = 'Print Sale: ' . $model->idText;
$this->params['breadcrumbs'][] = ['label' => 'Penjualan', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idText, 'url' => ['view', 'id' => $model->id]];
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

<div class="detail-view-container" style="padding:<?= isset($to_pdf) && $to_pdf ? '0px' : '20px' ?>">
    <div class="printable">

        <?= /* !isset($to_pdf) || !$to_pdf ? */ ReportHelper::header($params) /* . '<br>' : '' */ ?>

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
                        echo ReportHelper::header($params);
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

        <div class="row">
            <div class="col-sm-12">
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

        <div class="row" style="display:none">
            <div class="col-sm-12">
                <?php 
                $dataProvider = new \yii\data\ActiveDataProvider([
                    'query' => OutgoingItem::find()->where(['outgoing_id' => $model->id]),
                    'pagination' => false,
                    'sort' => [
                        'defaultOrder' => [
                            'id' => SORT_DESC,
                        ]
                    ],
                ]);

                /* echo \kartik\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'pjax' => true,
                    'hover' => false,
                    'striped' => false,
                    'bordered' => false,
                    'responsive' => false,
                    'responsiveWrap' => false,
                    'summary' => false,
                    'panel' => false,
                    'pjaxSettings' => ['options' => ['id' => 'grid']],
                    'tableOptions' => ['class' => 'table-report', 'style' => 'margin-bottom:0; border-bottom:1px solid #000; border-top:1px solid #000; display:none'],
                    'columns' => [
                        [
                            'attribute' => 'box_number',
                            'headerOptions' => ['style' => 'padding:2px 5px; text-align:right; width:1px; white-space:nowrap'],
                            'contentOptions' => ['style' => 'padding:0 5px; text-align:right; width:1px; white-space:nowrap'],
                        ],
                        [
                            'attribute' => 'item_id',
                            'value' => 'item.name',
                            'headerOptions' => ['style' => 'padding:2px 5px; text-align:left; width:1px; white-space:nowrap'],
                            'contentOptions' => ['style' => 'padding:0 5px; text-align:left; width:1px; white-space:nowrap'],
                        ],
                        [
                            'header' => 'Brand',
                            'value' => 'item.brand',
                            'headerOptions' => ['style' => 'padding:2px 5px; text-align:left; width:1px; white-space:nowrap'],
                            'contentOptions' => ['style' => 'padding:0 5px; text-align:left; width:1px; white-space:nowrap'],
                        ],
                        [
                            'header' => 'Type',
                            'value' => 'item.type',
                            'headerOptions' => ['style' => 'padding:2px 5px; text-align:left; width:1px; white-space:nowrap'],
                            'contentOptions' => ['style' => 'padding:0 5px; text-align:left; width:1px; white-space:nowrap'],
                        ],
                        [
                            'attribute' => 'quantity',
                            'label' => 'Qty',
                            'format' => ['decimal', 0],
                            'headerOptions' => ['style' => 'padding:2px 5px; text-align:right; width:1px; white-space:nowrap'],
                            'contentOptions' => ['style' => 'padding:0 5px; text-align:right; width:1px; white-space:nowrap'],
                        ],
                        [
                            'header' => '',
                            'value' => 'item.unit_of_measurement',
                            'headerOptions' => ['style' => 'padding:2px 5px; text-align:right; width:1px; white-space:nowrap'],
                            'contentOptions' => ['style' => 'padding:0 5px; text-align:right; width:1px; white-space:nowrap'],
                        ],
                        [
                            'attribute' => 'price',
                            'format' => ['decimal', 0],
                            'headerOptions' => ['style' => 'padding:2px 5px; text-align:right; width:1px; white-space:nowrap'],
                            'contentOptions' => ['style' => 'padding:0 5px; text-align:right; width:1px; white-space:nowrap'],
                        ],
                        [
                            'attribute' => 'subtotal',
                            'format' => ['decimal', 0],
                            'headerOptions' => ['style' => 'padding:2px 5px; text-align:right; width:1px; white-space:nowrap'],
                            'contentOptions' => ['style' => 'padding:0 5px; text-align:right; width:1px; white-space:nowrap'],
                        ],
                    ],
                ]); */
                ?>

                <?php /* if (!isset($to_pdf) || !$to_pdf) {  ?>
                <table width="100%" class="table-report-footer">
                    <tr>
                        <td width="65%"><big><big><b>INDAH MOTOR</b></big> <br> <i>Selama belum lunas, barang ini dianggap barang titipan.</i></big></td>
                        <td width="30%" class="text-center"><big><big><b><?= count($model->outgoingItems) ?></b> ITEM</big></big></td>
                        <td width="35%" class="text-right"><big><big>TOTAL : Rp <b><?= Yii::$app->formatter->asDecimal($model->total, 0) ?></b></big></big></td>
                    </tr>
                </table>
                <?php } */ ?>

            </div>
        </div>

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