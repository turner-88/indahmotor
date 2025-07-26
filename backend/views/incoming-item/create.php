<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\IncomingItem */

$this->title = 'Create Incoming Item';
$this->params['breadcrumbs'][] = ['label' => 'Incoming Item', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="incoming-item-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
    
</div>
