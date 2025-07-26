<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "item".
 *
 * @property integer $id
 * @property string $name
 * @property string $shortcode
 * @property string $brand
 * @property string $type
 * @property string $unit_of_measurement
 * @property double $initial_quantity
 * @property double $current_quantity
 * @property double $purchase_net_price
 * @property double $purchase_gross_price
 * @property double $purchase_discount
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property IncomingItem[] $incomingItems
 * @property Incoming[] $incomings
 * @property User $createdBy
 * @property User $updatedBy
 * @property ItemLocation[] $itemLocations
 * @property ItemPrice[] $itemPrices
 * @property OutgoingItem[] $outgoingItems
 * @property Outgoing[] $outgoings
 */
class Item extends \yii\db\ActiveRecord
{
    public $sum_quantity_in  = 0;
    public $sum_quantity_out = 0;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
            \yii\behaviors\BlameableBehavior::className(),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item';
    }

    public static function addStock($id, $quantity)
    {
        $model = self::findOne($id);
        $model->current_quantity = $model->current_quantity + $quantity;
        return $model->save() ? true : false;
    }

    public static function subtractStock($id, $quantity)
    {
        $model = self::findOne($id);
        $model->current_quantity = $model->current_quantity - $quantity;
        return $model->save() ? true : false;
    }

    public function getShortText()
    {
        return $this->shortcode
            . ' | ' . $this->name
            . ' | ' . $this->brand
            . ' | ' . $this->type;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['initial_quantity', 'current_quantity', 'purchase_net_price', 'purchase_gross_price', 'purchase_discount'], 'number'],
            [['name', 'shortcode', 'brand', 'type', 'unit_of_measurement', 'location'], 'string', 'max' => 191],
            [['shortcode'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nama',
            'shortcode' => 'Kode',
            'brand' => 'Merk',
            'type' => 'Type',
            'unit_of_measurement' => 'Satuan',
            'initial_quantity' => 'Stock Awal',
            'current_quantity' => 'Stock',
            'purchase_net_price' => 'Harga Net',
            'purchase_gross_price' => 'Harga List',
            'purchase_discount' => 'Discount',
            'location' => 'Lokasi',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomingItems()
    {
        return $this->hasMany(IncomingItem::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomings()
    {
        return $this->hasMany(Incoming::className(), ['id' => 'incoming_id'])->viaTable('incoming_item', ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemLocations()
    {
        return $this->hasMany(ItemLocation::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemPrices()
    {
        return $this->hasMany(ItemPrice::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutgoingItems()
    {
        return $this->hasMany(OutgoingItem::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutgoings()
    {
        return $this->hasMany(Outgoing::className(), ['id' => 'outgoing_id'])->viaTable('outgoing_item', ['item_id' => 'id']);
    }

    public function getPrices()
    {
        $return = '';
        $separator = '';

        // $return = '<table width="100%"><tr>';
        foreach ($this->itemPrices as $itemPrice) {
            /* $return.= '
                <td width="25%"><b>' . $itemPrice->priceGroup->name . '</b>
                ' . Yii::$app->formatter->asDecimal($itemPrice->price, 0) . '
                <span class="text-muted">' . $itemPrice->discount . '%</span></td>'; */
            $return.= $separator . '<b>' . $itemPrice->priceGroup->name . '</b>:' . Yii::$app->formatter->asDecimal($itemPrice->price, 0) . ' <span class="text-muted">(' . $itemPrice->discount . '%)</span>';
            $separator = ' | ';
        }
        // $return.= '</tr></table>';
        return $return;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockHistoryDailies()
    {
        return $this->hasMany(StockHistoryDaily::className(), ['item_id' => 'id']);
    }
}
