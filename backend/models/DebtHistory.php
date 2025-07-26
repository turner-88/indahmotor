<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "_debt_history".
 *
 * @property integer $customer_id
 * @property string $date
 * @property integer $outgoing_id
 * @property double $debt
 * @property integer $payment_id
 * @property integer $credit
 * @property integer $adjustment
 * @property integer $return
 */
class DebtHistory extends \yii\db\ActiveRecord
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
        return '_debt_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'outgoing_id', 'payment_id', 'credit', 'adjustment', 'return'], 'integer'],
            [['date', 'remark'], 'safe'],
            [['debt'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'customer_id' => 'Customer',
            'date' => 'Date',
            'outgoing_id' => 'Outgoing',
            'debt' => 'Debt',
            'payment_id' => 'Payment',
            'credit' => 'Credit',
            'adjustment' => 'Adjustment',
            'return' => 'Return',
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
