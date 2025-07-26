<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "outgoing_item".
 *
 * @property integer $id
 * @property integer $outgoing_id
 * @property integer $item_id
 * @property double $quantity
 * @property double $price
 * @property integer $discount
 * @property integer $is_taxable
 * @property double $adjustment
 * @property double $subtotal
 * @property integer $box_number
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property Incoming[] $incomings
 * @property User $createdBy
 * @property User $updatedBy
 * @property Item $item
 * @property Outgoing $outgoing
 */
class OutgoingItem extends \yii\db\ActiveRecord
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
        return 'outgoing_item';
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) Item::subtractStock($this->item_id, ($this->quantity ?? 0));
        if (!$insert && isset($changedAttributes['quantity'])) Item::subtractStock($this->item_id, ($this->quantity - $changedAttributes['quantity']));
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Item::addStock($this->item_id, $this->quantity);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['outgoing_id', 'item_id', 'quantity', 'price'], 'required'],
            [['outgoing_id', 'item_id', 'is_taxable', 'box_number', 'incoming_item_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['quantity', 'price', 'discount', 'adjustment', 'subtotal'], 'number'],
            // [['outgoing_id', 'item_id'], 'unique', 'targetAttribute' => ['outgoing_id', 'item_id'], 'message' => 'The combination of Outgoing and Item has already been taken.'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['outgoing_id'], 'exist', 'skipOnError' => true, 'targetClass' => Outgoing::className(), 'targetAttribute' => ['outgoing_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'outgoing_id' => 'Faktur',
            'item_id' => 'Item',
            'quantity' => 'Qty',
            'price' => 'Harga',
            'discount' => 'Diskon (%)',
            'is_taxable' => 'Is Taxable',
            'adjustment' => 'Adjustment',
            'subtotal' => 'Subtotal',
            'box_number' => 'Box',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomings()
    {
        return $this->hasMany(Incoming::className(), ['outgoing_item_id' => 'id']);
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
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutgoing()
    {
        return $this->hasOne(Outgoing::className(), ['id' => 'outgoing_id']);
    }

    public function getIncomingItemBefore()
    {
        $incomingItem = IncomingItem::findOne($this->incoming_item_id);

        if (!$incomingItem) {
            $incomingItem = IncomingItem::find()->joinWith(['incoming'])->where(['item_id' => $this->item_id])->andWhere(['<=', 'incoming.date', $this->outgoing->date])->orderBy('id DESC')->one();
            if (!$incomingItem) $incomingItem = IncomingItem::find()->joinWith(['incoming'])->where(['item_id' => $this->item_id])->orderBy('id ASC')->one();

            /* $outgoing_quantity_before = $this->item->initial_quantity;
            $outgoing_quantity_before+= OutgoingItem::find()->joinWith(['outgoing'])->where(['item_id' => $this->item_id])->andWhere([
                'or',
                [
                    'and',
                    ['<', 'outgoing.id', $this->outgoing->id],
                    ['<=', 'outgoing.date', $this->outgoing->date],
                ],
                ['<', 'outgoing.date', $this->outgoing->date],
            ])->sum('quantity');

            $incoming_quantity_before = IncomingItem::find()->joinWith(['incoming'])->where(['item_id' => $this->item_id])->andWhere(['<', 'incoming.date', $this->outgoing->date])->sum('quantity');
            $incomingItems            = IncomingItem::find()->joinWith(['incoming'])->where(['item_id' => $this->item_id])->andWhere(['<', 'incoming.date', $this->outgoing->date])->orderBy('id DESC')->all();
            
            $diff = $incoming_quantity_before - $outgoing_quantity_before;
            if ($diff <= 0 || !$incomingItems) {
                $incomingItemBefore = IncomingItem::find()->joinWith(['incoming'])->where(['item_id' => $this->item_id])->andWhere(['<', 'incoming.date', $this->outgoing->date])->orderBy('id DESC')->one();
                if (!$incomingItemBefore) $incomingItemBefore = IncomingItem::find()->joinWith(['incoming'])->where(['item_id' => $this->item_id])->andWhere(['<=', 'incoming.date', $this->outgoing->date])->orderBy('id DESC')->one();
                if (!$incomingItemBefore) $incomingItemBefore = IncomingItem::find()->joinWith(['incoming'])->where(['item_id' => $this->item_id])->orderBy('id ASC')->one();
                $incomingItem = $incomingItemBefore;
            } else {
                foreach ($incomingItems as $incomingItemBefore) {
                    $diff-= $incomingItemBefore->quantity;
                    if ($diff <= 0) {
                        $incomingItem = $incomingItemBefore;
                    }
                }
            } */

            if ($incomingItem) {
                $this->incoming_item_id = $incomingItem->id;
                $this->save(); 
            }
        }

        return $incomingItem;
    }
}
