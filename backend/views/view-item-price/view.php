<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\ViewItemPrice */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'View Item Price', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="view-item-price-view box-- box-info--">

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
                'nama_barang',
                'kode_barang',
                'merk',
                'tipe',
                'satuan',
                [
                    'attribute' => 'stok',
                    'format' => ['decimal', 2],
                ],
                'lokasi_penyimpanan',
                'harga_list:integer',
                [
                    'attribute' => 'diskon_pembelian',
                    'format' => ['decimal', 2],
                ],
                'harga_net:integer',
                [
                    'attribute' => 'diskon_A',
                    'format' => ['decimal', 2],
                ],
                'harga_A',
                [
                    'attribute' => 'diskon_B',
                    'format' => ['decimal', 2],
                ],
                'harga_B',
                [
                    'attribute' => 'diskon_C',
                    'format' => ['decimal', 2],
                ],
                'harga_C',
            ],
        ]) ?>
        </div>
    </div>
</div>
