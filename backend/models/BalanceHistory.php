<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "_balance_history".
 *
 * @property string $date
 * @property string $transaction_type
 * @property integer $transaction_id
 * @property string $in
 * @property string $out
 * @property string $remark
 */
class BalanceHistory extends \yii\db\ActiveRecord
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
        return '_balance_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['transaction_id'], 'integer'],
            [['remark'], 'string'],
            [['transaction_type'], 'string', 'max' => 1],
            [['in'], 'string', 'max' => 20],
            [['out'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'date' => 'Date',
            'transaction_type' => 'Transaction Type',
            'transaction_id' => 'Transaction',
            'in' => 'In',
            'out' => 'Out',
            'remark' => 'Remark',
        ];
    }
}
