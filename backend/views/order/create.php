<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Order */

$this->title = 'Order Baru';
$this->params['breadcrumbs'][] = ['label' => 'Order', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
    
</div>
