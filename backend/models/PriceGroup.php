<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "price_group".
 *
 * @property integer $id
 * @property string $name
 * @property double $default_discount
 * @property string $description
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property Customer[] $customers
 * @property IncomingItemPrice[] $incomingItemPrices
 * @property ItemPrice[] $itemPrices
 * @property User $createdBy
 * @property User $updatedBy
 */
class PriceGroup extends \yii\db\ActiveRecord
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
        return 'price_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['default_discount'], 'number'],
            [['description'], 'string'],
            [['created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name'], 'string', 'max' => 191],
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
            'name' => 'Name',
            'default_discount' => 'Default Discount',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomers()
    {
        return $this->hasMany(Customer::className(), ['price_group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomingItemPrices()
    {
        return $this->hasMany(IncomingItemPrice::className(), ['price_group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemPrices()
    {
        return $this->hasMany(ItemPrice::className(), ['price_group_id' => 'id']);
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
