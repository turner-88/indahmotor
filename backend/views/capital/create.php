<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Capital */

$this->title = 'Pemasukan Baru';
$this->params['breadcrumbs'][] = ['label' => 'Pemasukan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="capital-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
    
</div>
