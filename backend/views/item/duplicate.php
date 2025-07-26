<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Item */

$this->title = 'Duplikat Barang';
$this->params['breadcrumbs'][] = ['label' => 'Barang', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
		'modelOrigin' => $modelOrigin,
	]) ?>
    
</div>
