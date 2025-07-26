<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\OrderItem */

$this->title = 'Create Order Item';
$this->params['breadcrumbs'][] = ['label' => 'Order Item', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-item-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
    
</div>
