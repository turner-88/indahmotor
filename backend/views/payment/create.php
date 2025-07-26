<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Payment */

$this->title = Yii::t('app', 'Pembayaran Baru');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pembayaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
    
</div>
