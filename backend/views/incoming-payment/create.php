<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\IncomingPayment */

$this->title = 'Create Incoming Payment';
$this->params['breadcrumbs'][] = ['label' => 'Incoming Payment', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="incoming-payment-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
    
</div>
