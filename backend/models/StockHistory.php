<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "_stock_history".
 *
 * @property integer $item_id
 * @property string $date
 * @property string $transaction_type
 * @property integer $transaction_id
 * @property string $supplier
 * @property string $customer
 * @property double $quantity
 */
class StockHistory extends \yii\db\ActiveRecord
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
        return '_stock_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'transaction_id'], 'integer'],
            [['date'], 'safe'],
            [['quantity'], 'number'],
            [['transaction_type'], 'string', 'max' => 1],
            [['supplier', 'customer'], 'string', 'max' => 191],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_id' => 'Item',
            'date' => 'Date',
            'transaction_type' => 'Transaction Type',
            'transaction_id' => 'Transaction',
            'supplier' => 'Supplier',
            'customer' => 'Customer',
            'quantity' => 'Quantity',
        ];
    }

    public function getItem()  
    {  
        return $this->hasOne(Item::className(), ['id' => 'item_id']);  
    }
}
