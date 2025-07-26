 
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

/* @var $this yii\web\View */
/* @var $model backend\models\Outgoing */

$this->title = $title;  
$this->params['breadcrumbs'][] = ['label' => 'Penjualan', 'url' => ['index']];
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
    
    <?= DateRangePicker::widget([
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
    ]); ?>

    <div style="display:inline-block; width:200px; vertical-align:bottom">
    <?= Select2::widget([
        'name' => 'customer_id',
        'value' => $customer_id,
        'data' => ArrayHelper::map(Customer::find()->all(), 'id', 'shortText'),
        'options' => ['placeholder' => 'pilih customer'],
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
        'customer_id'   => $customer_id,
        'to_pdf'        => 1,
    ]) ?>" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print Rekap </a>
    
    <a id="print-with-item" target="_blank" href="<?= Url::to(['print-all',
        'date_start'    => $date_start,
        'date_end'      => $date_end,
        'customer_id'   => $customer_id,
        'to_pdf'        => 1,
    ]) ?>" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print Faktur </a>


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
        
        <big>
        <table class="table table-report">
            <thead>
            <tr class="thead" style="border-bottom:2px solid #eee;">
                <td width="20%">&nbsp;</td>
                <td class="text-right" style="width:1px">No</td>
                <td>Faktur</td>
                <?php if (!$customer_id) { ?><td>Pelanggan</td><?php } ?>        
                <?php if ($date_start != $date_end) { ?><td>Tanggal</td><?php } ?>
                <td class="text-right">Jumlah</td>
                <td width="20%">&nbsp;</td>
            </tr>
            </thead>

        <?php
        $i = 0;
        $total = 0;
        $due_date_base = '0000-00-00';
        foreach ($models as $model) {
            $total += $model->total;
            if ($due_date_base < $model->date) $due_date_base = $model->date;
        ?>
            <tr>
                <td width="20%">&nbsp;</td>
                <td class="text-right" style="width:1px"><?= ++$i ?></td>
                <td><?= $model->idText ?></td>
	        	<?php if (!$customer_id) { ?><td><?= $model->customer->name ?></td><?php } ?>
                <?php if ($date_start != $date_end) { ?><td><?= Yii::$app->formatter->asDate($model->date) ?></td><?php } ?>
                <td class="text-right"><?= Yii::$app->formatter->asDecimal($model->total, 0) ?></td>
                <td width="20%">&nbsp;</td>
            </tr>
        <?php } ?>
        </table>
        </big>

        <table class="table table-report-footer">
            <tr>
                <td colspan="2" class="">
                    <big>
                        <?php if ($models && $models[0]->customer->payment_limit_duration) {
                            $date = new DateTime($due_date_base);
                            $date->add(new DateInterval('P'.$models[0]->customer->payment_limit_duration.'D'));
                            $due_date = $date->format('d/m/Y');
                            echo '<i>Jatuh tempo pembayaran ' . $models[0]->customer->payment_limit_duration . ' hari</i><br>(<b>'.$due_date.'</b>)';
                        } ?>
                    </big>
                </td>
                <td class="text-right" style="width:40%; white-space:nowrap"><big>TOTAL : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <big><b><?= Yii::$app->formatter->asDecimal($total, 0) ?></b></big></big></td>
                <td width="20%">&nbsp;</td>
            </tr>
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