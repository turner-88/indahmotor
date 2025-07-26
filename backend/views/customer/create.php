<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Customer */

$this->title = 'Create Pelanggan';
$this->params['breadcrumbs'][] = ['label' => 'Pelanggan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
    
</div>
