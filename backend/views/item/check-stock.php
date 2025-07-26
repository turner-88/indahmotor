<?php

use backend\models\IncomingItem;
use backend\models\Item;
use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\PriceGroup;
use backend\models\ItemPrice;
use backend\models\OutgoingItem;

/* @var $this yii\web\View */
/* @var $model backend\models\Item */

$this->title = 'Check Stock';
$this->params['breadcrumbs'][] = ['label' => 'Barang', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$items = Item::find()->orderBy('current_quantity DESC')->all();
?>

<?php foreach ($items as $item) { ?>
<?php 
    $sum_incoming = IncomingItem::find()->where(['item_id' => $item->id])->sum('quantity');
    $sum_outgoing = OutgoingItem::find()->where(['item_id' => $item->id])->sum('quantity');
    $sum_diff     = $sum_incoming - $sum_outgoing;
?>
    <div class="box box-body">
        <?= $item->name ?>
        <br><?= $item->current_quantity ?>
        <br><?= $sum_incoming ?> - <?= $sum_outgoing ?> = <?= $sum_diff ?>
    </div>
<?php } ?>
