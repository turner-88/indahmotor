<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "payment".
 *
 * @property integer $id
 * @property integer $incoming_id
 * @property integer $supplier_id
 * @property integer $outgoing_id
 * @property integer $customer_id
 * @property string $date
 * @property integer $amount
 * @property integer $adjustment
 * @property integer $return
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property Customer $customer
 * @property User $createdBy
 * @property User $updatedBy
 */
class Payment extends \yii\db\ActiveRecord
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
        return 'payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date'], 'required'],
            [['incoming_id', 'supplier_id', 'outgoing_id', 'customer_id', 'amount', 'adjustment', 'return', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['date'], 'safe'],
            [['remark'], 'string'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
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
            'incoming_id' => 'Faktur Pembelian',
            'supplier_id' => 'Supplier',
            'outgoing_id' => 'Faktur Penjualan',
            'customer_id' => 'Customer',
            'date' => 'Date',
            'amount' => 'Jumlah Bayar',
            'adjustment' => 'Diskon Bayar',
            'return' => 'Return',
            'remark' => 'Keterangan',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
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
    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutgoing()
    {
        return $this->hasOne(Outgoing::className(), ['id' => 'outgoing_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
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

    public function beforeSave($insert)
    {
        if ($this->incoming_id) {
            $this->supplier_id = Incoming::findOne($this->incoming_id)->supplier_id;
        }
        if ($this->outgoing_id) {
            $this->customer_id = Outgoing::findOne($this->outgoing_id)->customer_id;
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->customer_id) {
			$customer = Customer::findOne($this->customer_id);
			if ($customer) $customer->setPaymentStatus();
		}
        if ($this->supplier_id) {
			$supplier = Supplier::findOne($this->supplier_id);
			if ($supplier) $supplier->setPaymentStatus();
		}
        return parent::afterSave($insert, $changedAttributes);
    }
}
