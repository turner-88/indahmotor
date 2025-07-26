<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use kartik\grid\GridView;
use kartik\date\DatePicker;
use kartik\datecontrol\DateControl;
use backend\models\Supplier;
use backend\models\OrderItem;
use backend\models\Item;


/* @var $this yii\web\View */
/* @var $model backend\models\Order */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Order', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="order-view box-- box-info--">

    <div class="box-body--">
        <p>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> '. 'Update', ['update', 'id' => $model->id], [
            'class' => 'btn btn-warning',
        ]) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> ' . 'Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        </p>

        <div class="detail-view-container">
        <?= DetailView::widget([
            'options' => ['class' => 'table detail-view'],
            'model' => $model,
            'attributes' => [
                // 'id',
                'customer_name',
                'date',
                // 'created_at:datetime',
                // 'updated_at:datetime',
                // 'createdBy.username:text:Created By',
                // 'updatedBy.username:text:Updated By',
            ],
        ]) ?>
        </div>

        <?php 
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => OrderItem::find()->where(['order_id' => $model->id]),
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        echo \kartik\grid\GridView::widget([
            'dataProvider' => $dataProvider,
                    // 'pjax' => true,
            'hover' => true,
            'striped' => true,
            'bordered' => false,
            'summary' => false,
            'toolbar' => [
                Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success']),
                Html::a('<i class="fa fa-repeat"></i> ' . Yii::t('app', 'Reload'), ['index'], ['data-pjax' => 0, 'class' => 'btn btn-default']),
                '{toggleData}',
                        // $exportMenu,
            ],
            'panel' => false,
            'pjaxSettings' => ['options' => ['id' => 'grid']],
                    // 'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table table-condensed'],
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                ],
                'item_shortcode',
                'item_name',
                'unit_of_measurement',
                [
                    'attribute' => 'quantity',
                    'format' => 'integer',
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                ],
                [
                    'attribute' => 'to_be_ordered',
                    'format' => 'integer',
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                ],
                [
                    'attribute' => 'supplier_id',
                    'value' => 'supplier.name',
                ],
                'type',
                'brand_storage',
                'brand_supplier',
            ],
        ]);
        ?>
    </div>
</div>
