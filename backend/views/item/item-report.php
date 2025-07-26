 
<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\daterange\DateRangePicker;
use backend\models\Item;
use backend\helpers\ReportHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\Outgoing */

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Barang', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
?>

<?php if (!isset($to_pdf) || !$to_pdf) { ?>

<style>
    .kv-grid-container {border:none !important; overflow: hidden; margin-bottom: 5px; padding-bottom: 5px}
    .table-report {width:100%}
    .table-report td {padding:0px 5px !important}
</style>

<?php $form = ActiveForm::begin(['action' => Url::to([$view]), 'method' => 'get', 'options' => ['style' => 'display:inline']]); ?>

    <div style="display:none; width:200px; vertical-align:bottom">
        <?php /* Select2::widget([
            'name' => 'shelf',
            'value' => $shelf,
            'data' => ArrayHelper::map(Item::find()->distinct('location')->all(), 'location', 'location'),
            'options' => ['placeholder' => 'semua lokasi'],
            'pluginOptions' => ['allowClear' => true],
        ]); */ ?>
    </div>

    <div style="display:inline-block; width:200px; vertical-align:bottom">
        <label for="shelf">Lokasi</label>
        <?= Html::textInput('shelf', $shelf, ['class' => 'form-control']) ?>
    </div>

    <div style="display:inline-block; width:200px; vertical-align:bottom">
        <label for="shortcode">Kode</label>
        <?= Html::textInput('shortcode', $shortcode, ['class' => 'form-control']) ?>
    </div>

    <div style="display:inline-block; width:200px; vertical-align:bottom">
        <label for="name">Nama Barang</label>
        <?= Html::textInput('name', $name, ['class' => 'form-control']) ?>
    </div>

    <div style="display:inline-block; width:200px; vertical-align:bottom">
        <label for="brand">Merk</label>
        <?= Html::textInput('brand', $brand, ['class' => 'form-control']) ?>
    </div>
    
    <div style="display:inline-block; width:200px; vertical-align:bottom">
        <label for="type">Tipe</label>
        <?= Html::textInput('type', $type, ['class' => 'form-control']) ?>
    </div>
    
    <div style="display:inline-block; width:150px; vertical-align:bottom" class="form-control">
        <?= Html::checkbox('ready_stock_only', $ready_stock_only, ['label' => 'Ada Stock saja']) ?>
    </div>
    
    <?= Html::button('<i class="glyphicon glyphicon-refresh"></i> ' . Yii::t('app', 'Reload'), [
        'type' => 'submit',
        'class' => 'btn btn-default',
        // 'style' => 'border-top-left-radius:0; border-bottom-left-radius:0; margin-left:-1px',
    ]) ?>

    <?php ActiveForm::end(); ?>
    
    <button style="display:none" onclick="window.print()" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print</button>
    <a id="print" target="_blank" href="<?= Url::to([$view,
        'shelf'            => $shelf,
        'shortcode'        => $shortcode,
        'name'             => $name,
        'brand'            => $brand,
        'type'             => $type,
        'ready_stock_only'  => $ready_stock_only,
        'to_pdf'            => 1,
    ]) ?>" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print </a>

    <p></p>

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
                <td>Kode</td>
                <td>Nama Barang</td>
                <td>Merk</td>
                <td>Type</td>
                <?php if (!$shelf) { ?><td>Lokasi</td><?php } ?>
                <td class="text-right">Stock</td>
                <td class="text-right">Harga Satuan</td>
                <td class="text-right">Harga Total</td>
            </tr>
            </thead>

        <?php
        $i = 0;
        $total = 0;
        foreach ($models as $model) {
            $total+= $model->current_quantity * $model->purchase_net_price;
        ?>
            <tr>
                <td class="text-right" style="width:1px"><?= ++$i ?></td>
                <td><?= $model->shortcode ?></td>
                <td><?= $model->name ?></td>
                <td><?= $model->brand ?></td>
                <td><?= $model->type ?></td>
                <?php if (!$shelf) { ?><td><?= $model->location ?></td><?php } ?>
                <td class="text-right"><?= Yii::$app->formatter->asDecimal($model->current_quantity, 0) ?></td>
                <td class="text-right"><?= Yii::$app->formatter->asDecimal($model->purchase_net_price, 0) ?></td>
                <td class="text-right"><?= Yii::$app->formatter->asDecimal($model->current_quantity * $model->purchase_net_price, 0) ?></td>
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