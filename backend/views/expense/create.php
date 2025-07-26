<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Expense */

$this->title = 'Pengeluaran Baru';
$this->params['breadcrumbs'][] = ['label' => 'Pengeluaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expense-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
    
</div>
