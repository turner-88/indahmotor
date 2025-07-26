<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\OutgoingItem */

$this->title = 'Create Outgoing Item';
$this->params['breadcrumbs'][] = ['label' => 'Outgoing Item', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outgoing-item-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
    
</div>
