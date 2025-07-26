<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\ViewItemPrice */

$this->title = 'Create View Item Price';
$this->params['breadcrumbs'][] = ['label' => 'View Item Price', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="view-item-price-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
    
</div>
