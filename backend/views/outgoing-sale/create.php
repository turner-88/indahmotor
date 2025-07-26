<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Outgoing */

$this->title = 'Penjualan Baru';
$this->params['breadcrumbs'][] = ['label' => 'Penjualan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outgoing-create box-- box-success--">
	<!-- <div class="box-header"></div> -->

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
    
</div>

<?php 
    $this->registerJsFile(
        '@web/js/outgoing-sale-create.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>