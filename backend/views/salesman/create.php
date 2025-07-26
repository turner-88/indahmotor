<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Salesman */

$this->title = 'Create Salesman';
$this->params['breadcrumbs'][] = ['label' => 'Salesman', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="salesman-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
    
</div>
