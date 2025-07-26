<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "_stock_history_daily".
 *
 * @property string $date
 * @property int $item_id
 * @property double $quantity_in
 * @property double $quantity_out
 */
class StockHistoryDaily extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '_stock_history_daily';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['item_id'], 'integer'],
            [['quantity_in', 'quantity_out'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'date' => 'Date',
            'item_id' => 'Item ID',
            'quantity_in' => 'Quantity In',
            'quantity_out' => 'Quantity Out',
        ];
    }

    public function getItem()  
    {  
        return $this->hasOne(Item::className(), ['id' => 'item_id']);  
    }
}
