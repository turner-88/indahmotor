<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "incoming_item_price".
 *
 * @property integer $id
 * @property integer $incoming_item_id
 * @property integer $price_group_id
 * @property double $discount
 * @property double $price
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property IncomingItem $incomingItem
 * @property PriceGroup $priceGroup
 * @property User $createdBy
 * @property User $updatedBy
 */
class IncomingItemPrice extends \yii\db\ActiveRecord
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
        return 'incoming_item_price';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['incoming_item_id', 'price_group_id', 'discount', 'price'], 'required'],
            [['incoming_item_id', 'price_group_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['discount', 'price'], 'number'],
            [['incoming_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => IncomingItem::className(), 'targetAttribute' => ['incoming_item_id' => 'id']],
            [['price_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => PriceGroup::className(), 'targetAttribute' => ['price_group_id' => 'id']],
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
            'incoming_item_id' => 'Incoming Item',
            'price_group_id' => 'Price Group',
            'discount' => 'Discount',
            'price' => 'Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomingItem()
    {
        return $this->hasOne(IncomingItem::className(), ['id' => 'incoming_item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceGroup()
    {
        return $this->hasOne(PriceGroup::className(), ['id' => 'price_group_id']);
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
}
