<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Import Barang');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Barang'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box panel-body">

	<div class="row">
    <div class="package-form col-md-6">

	    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

	    <div class="form-group">
	    <?php 
	        echo FileInput::widget([
	            'id' => 'package-file',
	            'name' => 'package-file',
	            'options' => ['accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
	        ]);
	    ?>
	    </div>

	    <?php ActiveForm::end(); ?>

	</div>
	</div>

	<style type="text/css">
		.table-excel {
			font-family: sans-serif;
		}
		.table-excel th {
			text-align: center;
			color: #999;
		}
		.table-excel tr.data-header td {
			/*font-weight: bold;*/
			text-align: center;
			background: #ff06;
		}
	</style>

	<div style="display:none">
	<p>Format file excel yang didukung: </p>
	<div class="row">
	<div class="col-md-12">
	<table class="table table-bordered table-condensed table-excel small text-muted text-center" style="background: #fff">
		<tr>
			<th width="40px" style="background: #e7e7e7">&nbsp;</th>
			<th>A</th>
			<th>B</th>
			<th>C</th>
			<th>D</th>
			<th>E</th>
			<th>F</th>
			<th>G</th>
			<th>H</th>
			<th>I</th>
			<th>J</th>
			<th>K</th>
			<th>L</th>
			<th>M</th>
			<th>N</th>
			<th>O</th>
			<th>P</th>
			<th>Q</th>
			<th>R</th>
			<th>S</th>
			<th>T</th>
			<th>U</th>
			<th>V</th>
			<th>W</th>
		</tr>
		<tr class="data-header">
			<th>1</th>
			<td>Tgl Barang Datang</td>
			<td>Tgl. Terima IR</td>
			<td>PO</td>
			<td>GR</td>
			<td>No Mat</td>
			<td>MRP Type</td>
			<td>Qty IR </td>
			<td>Nama Barang</td>
			<td>Pemasok</td>
			<td>Staff Perencanaan</td>
			<td>Int/Ext</td>
			<td>Preparer</td>
			<td>Start (Rc Cek Barang)</td>
			<td>Finish (Rc Cek Barang)</td>
			<td>Start (Rc Approval)</td>
			<td>Finish (Rc Approval)</td>
			<td>Start (Rl Cek Barang)</td>
			<td>Finish (Rl Cek Barang)</td>
			<td>Start (Rl Approval)</td>
			<td>Finish (Rl Approval)</td>
			<td>Tanggal Kembali ke Gudang</td>
			<td>Ket</td>
			<td>TINDAK LANJUT</td>



		</tr>
		<tr>
			<th>2</th>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<th>3</th>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</table>
	</div>
	</div>
	</div>

	<p>
		Keterangan:
		<ul>
			<li>Data dimulai pada row 2 dan seterusnya (row 1 sebagai header).</li>
			<li>Format data kolom Tanggal berupa date.</li>
		</ul>
	</p>

</div>