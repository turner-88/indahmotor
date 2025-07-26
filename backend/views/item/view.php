<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\PriceGroup;
use backend\models\ItemPrice;

/* @var $this yii\web\View */
/* @var $model backend\models\Item */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Barang', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="item-view box-- box-info--">

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

        <?= Html::a('<i class="glyphicon glyphicon-clock"></i> '. 'History Stok', ['stock-history', 'item_id' => $model->id], [
            'class' => 'btn btn-default',
        ]) ?>
        </p>

        <div class="detail-view-container">
        <?= DetailView::widget([
            'options' => ['class' => 'table detail-view'],
            'model' => $model,
            'attributes' => [
                // 'id',
                'name',
                'shortcode',
                'brand',
                'type',
                'unit_of_measurement',
                [
                    'attribute' => 'current_quantity',
                    'format' => ['decimal', 0],
                ],
                [
                    'attribute' => 'purchase_net_price',
                    'format' => ['decimal', 2],
                ],
                [
                    'attribute' => 'purchase_gross_price',
                    'format' => ['decimal', 2],
                ],
                [
                    'attribute' => 'purchase_discount',
                    'format' => ['decimal', 2],
                ],
                'location',
            ],
        ]) ?>
        </div>

        <h4 style="margin-top:0">Price Group</h4>
        <div class="detail-view-container">
            <table class="table" style="margin-bottom:0">
                <tr>
                    <th class="text-right action-column" style="vertical-align:middle"><b>Group</b></th>
                    <th class="text-right action-column">Discount</th>
                    <th>Price</th>
                </tr>
            <?php foreach (($priceGroups = PriceGroup::find()->all()) as $priceGroup) { ?>
                <tr>
                    <td class="text-right action-column" style="vertical-align:middle"><b><?= $priceGroup->name ?></b></td>
                    <td class="text-right action-column"><?= (($itemPrice = ItemPrice::findOne(['item_id' => $model->id, 'price_group_id' => $priceGroup->id])) !== null) ? $itemPrice->discount : '' ?></td>
                    <td><?= (($itemPrice = ItemPrice::findOne(['item_id' => $model->id, 'price_group_id' => $priceGroup->id])) !== null) ? Yii::$app->formatter->asDecimal($itemPrice->price, 0) : '' ?></td>
                </tr>
            <?php 
        } ?>
            </table>
        </div>
        
    </div>
</div>
