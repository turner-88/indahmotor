<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Supplier */

$this->title = 'Create Distributor';
$this->params['breadcrumbs'][] = ['label' => 'Distributor', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="supplier-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
    
</div>
