<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "incoming_item".
 *
 * @property integer $id
 * @property integer $incoming_id
 * @property integer $item_id
 * @property double $quantity
 * @property double $price
 * @property double $subtotal
 * @property double $discount
 * @property double $gross_price
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property Incoming $incoming
 * @property Item $item
 * @property IncomingItemPrice[] $incomingItemPrices
 * @property Outgoing[] $outgoings
 */
class IncomingItem extends \yii\db\ActiveRecord
{
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
        return 'incoming_item';
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Item::addStock($this->item_id, ($this->quantity - $changedAttributes['quantity']));
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Item::subtractStock($this->item_id, $this->quantity);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['incoming_id', 'item_id', 'quantity'], 'required'],
            [['incoming_id', 'item_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['quantity', 'price', 'subtotal', 'discount', 'gross_price'], 'number'],
            // [['incoming_id', 'item_id'], 'unique', 'targetAttribute' => ['incoming_id', 'item_id'], 'message' => 'The combination of Incoming and Item has already been taken.'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['incoming_id'], 'exist', 'skipOnError' => true, 'targetClass' => Incoming::className(), 'targetAttribute' => ['incoming_id' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'incoming_id' => 'Incoming',
            'item_id' => 'Item',
            'quantity' => 'Qty',
            'price' => 'Harga Net',
            'subtotal' => 'Subtotal',
            'discount' => 'Diskon (%)',
            'gross_price' => 'Harga List',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
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
    public function getIncoming()
    {
        return $this->hasOne(Incoming::className(), ['id' => 'incoming_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomingItemPrices()
    {
        return $this->hasMany(IncomingItemPrice::className(), ['incoming_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutgoings()
    {
        return $this->hasMany(Outgoing::className(), ['incoming_item_id' => 'id']);
    }
}
