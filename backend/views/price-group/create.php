<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\PriceGroup */

$this->title = 'Create Price Group';
$this->params['breadcrumbs'][] = ['label' => 'Price Group', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="price-group-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
    
</div>
