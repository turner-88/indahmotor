<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Config */

$this->title = 'Create Config';
$this->params['breadcrumbs'][] = ['label' => 'Config', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
    
</div>
