<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Outgoing */

$this->title = 'Create Outgoing';
$this->params['breadcrumbs'][] = ['label' => 'Outgoing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outgoing-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
    
</div>
