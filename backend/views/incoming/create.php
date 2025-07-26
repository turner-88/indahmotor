<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Incoming */

$this->title = 'Create Incoming';
$this->params['breadcrumbs'][] = ['label' => 'Incoming', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="incoming-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
    
</div>
