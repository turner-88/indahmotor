<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "order_item".
 *
 * @property integer $id
 * @property integer $order_id
 * @property string $item_name
 * @property integer $quantity
 * @property integer $to_be_ordered
 * @property integer $supplier_id
 * @property integer $item_id
 * @property string $item_shortcode
 * @property string $brand_storage
 * @property string $brand_supplier
 * @property string $type
 * @property string $unit_of_measurement
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property Order $order
 * @property Item $item
 * @property User $createdBy
 * @property User $updatedBy
 * @property Supplier $supplier
 */
class OrderItem extends \yii\db\ActiveRecord
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
        return 'order_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'item_name', 'quantity', 'to_be_ordered'], 'required'],
            [['order_id', 'quantity', 'to_be_ordered', 'supplier_id', 'item_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['item_name', 'item_shortcode', 'brand_storage', 'brand_supplier', 'type', 'unit_of_measurement'], 'string', 'max' => 191],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order',
            'item_name' => 'Nama Barang',
            'quantity' => 'Order Plgn',
            'to_be_ordered' => 'Order Dist',
            'supplier_id' => 'Distributor',
            'item_id' => 'Item',
            'item_shortcode' => 'Kode',
            'brand_storage' => 'Merk Gdg',
            'brand_supplier' => 'Merk Dist',
            'type' => 'Type',
            'unit_of_measurement' => 'Satuan',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
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
    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }
}
