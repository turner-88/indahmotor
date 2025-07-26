<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\daterange\DateRangePicker;
use backend\models\Supplier;
use backend\models\Order;
use backend\models\OrderItem;
use backend\helpers\ReportHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\Order */

$this->title = 'Order Distributor';
$this->params['breadcrumbs'][] = ['label' => 'Order', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php if (!$to_pdf) { ?>

<div class="order-view box-- box-info--">

    <div class="box-body--">
        
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
            'name' => 'supplier_id',
            'value' => $supplier_id,
            'data' => ArrayHelper::map(Supplier::find()->all(), 'id', 'name'),
            'options' => ['placeholder' => 'semua distributor'],
            'pluginOptions' => ['allowClear' => true],
        ]); ?>
        </div>

        <div style="display:inline-block; width:200px; vertical-align:bottom">
        <?= Select2::widget([
            'name' => 'customer_name',
            'value' => $customer_name,
            'data' => ArrayHelper::map(Order::find()->distinct('customer_name')->all(), 'customer_name', 'customer_name'),
            'options' => ['placeholder' => 'semua pelanggan'],
            'pluginOptions' => ['allowClear' => true],
        ]); ?>
        </div>
        
        <div style="display:inline-block; width:200px; vertical-align:bottom">
        <?= Select2::widget([
            'name' => 'brand_supplier',
            'value' => $brand_supplier,
            'data' => ArrayHelper::map(OrderItem::find()->distinct('brand_supplier')->all(), 'brand_supplier', 'brand_supplier'),
            'options' => ['placeholder' => 'semua merk'],
            'pluginOptions' => ['allowClear' => true],
        ]); ?>
        </div>

        <div style="display:inline-block; width:150px; vertical-align:bottom" class="form-control">
            <?= Html::checkbox('order_by_brand', $order_by_brand, ['label' => 'Urutkan Merk']) ?>
        </div>

        <?= Html::button('<i class="glyphicon glyphicon-refresh"></i> ' . Yii::t('app', 'Reload'), [
            'type' => 'submit',
            'class' => 'btn btn-default',
            // 'style' => 'border-top-left-radius:0; border-bottom-left-radius:0; margin-left:-1px',
        ]) ?>

		<?php ActiveForm::end(); ?>
        
        <button onclick="window.print()" style="display:none" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print</button>
        <a id="print" target="_blank" href="<?= Url::to([$view,
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'supplier_id'   => $supplier_id,
            'customer_name' => $customer_name,
            'brand_supplier'=> $brand_supplier,
            'order_by_brand'=> $order_by_brand,
            'to_pdf'        => 1,
        ]) ?>" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print </a>

        <p></p>

        <p class="form-panel"><b>HARGA NET:</b> <?= Yii::$app->formatter->asDecimal($total_net_price, 0) ?>, <b>HARGA LIST:</b> <?= Yii::$app->formatter->asDecimal($total_gross_price, 0) ?></p>

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
                    <?php if (!$supplier_id) { ?><td>Distributor</td><?php } ?>
                    <?php if (!$customer_name) { ?><td>Pelanggan</td><?php } ?>
	        		<td>Item</td>
                    <?php if (!$brand_supplier) { ?><td>Merk</td><?php } ?>
	        		<td>Type</td>
                    <td class="text-right">Qty</td>                    
	        		<td></td>
                </tr>
                </thead>

	        <?php
            $i = 0;
            foreach ($models as $model) {
            ?>
		    	<tr>
		    		<td class="text-right" style="width:1px"><?= ++$i ?></td>
	        		<?php if (!$supplier_id) { ?><td><?= $model->supplier ? $model->supplier->name : '' ?></td><?php } ?>
	        		<?php if (!$customer_name) { ?><td><?= $model->order->customer_name ?></td><?php } ?>
	        		<td><?= $model->item_name ?></td>
	        		<?php if (!$brand_supplier) { ?><td><?= $model->brand_supplier ?></td><?php } ?>
	        		<td><?= $model->type ?></td>
	        		<td class="text-right"><?= $model->to_be_ordered ?></td>
	        		<td><?= $model->unit_of_measurement ?></td>
		    	</tr>
		    <?php } ?>
            </table>

            <?php } ?>
            
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