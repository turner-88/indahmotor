<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "_transaction_history".
 */
class TransactionHistory extends \yii\db\ActiveRecord
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
        return '_transaction_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'amount', 'adjustment', 'return'], 'integer'],
            [['type', 'date', 'remark'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => 'Jenis',
            'date' => 'Tanggal',
            'customer_id' => 'Customer',
            'amount' => 'Jumlah',
            'adjustment' => 'Potongan',
            'return' => 'Retur',
            'remark' => 'Keterangan',
        ];
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
    public function getOutgoing() 
    { 
        return $this->hasOne(Outgoing::className(), ['id' => 'outgoing_id']); 
    }

    /** 
    * @return \yii\db\ActiveQuery 
    */ 
    public function getPayment() 
    { 
        return $this->hasOne(Payment::className(), ['id' => 'payment_id']); 
    }
}
