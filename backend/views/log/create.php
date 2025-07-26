<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Log */

$this->title = 'Create Log';
$this->params['breadcrumbs'][] = ['label' => 'Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-create box box-success">
	<div class="box-header"></div>

    <div class="box-body">
	    <?= $this->render('_form', [
	        'model' => $model,
	    ]) ?>
    </div>

</div>
