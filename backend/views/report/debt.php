 
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
use backend\helpers\ReportHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\Outgoing */

$this->title = $title;
$this->params['breadcrumbs'][] = 'Report';
$this->params['breadcrumbs'][] = $title;
?>

<?php if (!isset($to_pdf) || !$to_pdf) { ?>

<style>
    .kv-grid-container {border:none !important; overflow: hidden; margin-bottom: 5px; padding-bottom: 5px}
    .table-report {width:100%}
    .table-report td {padding:0px 5px !important}
</style>

<?php $form = ActiveForm::begin(['action' => Url::to([$view]), 'method' => 'get', 'options' => ['style' => 'display:inline']]); ?>
    
    <div style="display:inline-block; width:200px; vertical-align:bottom">
    <?= Select2::widget([
        'name' => 'salesman_id',
        'value' => $salesman_id,
        'data' => ArrayHelper::map(Salesman::find()->all(), 'id', 'name'),
        'options' => ['placeholder' => 'semua salesman'],
        'pluginOptions' => ['allowClear' => true],
    ]); ?>
    </div>

    <?php if (Yii::$app->user->can('owner')) { ?>
    <div style="display:inline-block; width:150px; vertical-align:bottom" class="form-control">
        <?= Html::checkbox('hide_zero', $hide_zero, ['label' => 'Sembunyikan 0']) ?>
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
        'salesman_id'   => $salesman_id,
        'hide_zero'     => $hide_zero,
        'to_pdf'        => 1,
    ]) ?>" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print </a>

    &nbsp;&nbsp;&nbsp;<a href="<?= Url::to(['/report/debt-history']) ?>" class="btn btn-default"><i class="fa fa-th-list"></i> HISTORY PIUTANG </a>

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
                <!-- <td>ID</td> -->
                <td>Nama Customer</td>
                <td>Alamat</td>
                <td>Telp</td>
                <td>PIC</td>
                <?php if (!$salesman_id) { ?><td>Salesman</td><?php } ?>
                <td class="text-right">Jumlah</td>  
            </tr>
            </thead>

        <?php
        $i = 0;
        $total = 0;
        foreach ($models as $model) {
            $total += $model->debt;
            if (!$hide_zero || !$model->debt == 0) {
        ?>
            <tr>
                <td class="text-right" style="width:1px"><?= ++$i ?></td>
                <!-- <td><?= $model->id ?></td> -->
                <td><?= $model->name ?></td>
                <td><?= $model->address ?></td>
                <td><?= $model->phone ?></td>
                <td><?= $model->person_in_charge ?></td>
                <?php if (!$salesman_id) { ?><td><?= $model->salesman ? $model->salesman->name : '' ?></td><?php } ?>
                <td class="text-right"><?= Yii::$app->formatter->asDecimal($model->debt, 0) ?></td>
            </tr>
        <?php } 
        } ?>
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