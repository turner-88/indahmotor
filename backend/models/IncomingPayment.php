<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "incoming_payment".
 *
 * @property integer $id
 * @property integer $incoming_id
 * @property string $date
 * @property double $amount
 * @property integer $payment_type_id
 * @property string $reference
 * @property string $description
 * @property string $image
 * @property integer $is_deleted
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property Incoming $incoming
 * @property PaymentType $paymentType
 */
class IncomingPayment extends \yii\db\ActiveRecord
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
        return 'incoming_payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['incoming_id', 'date', 'amount', 'payment_type_id'], 'required'],
            [['incoming_id', 'payment_type_id', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['date'], 'safe'],
            [['amount'], 'number'],
            [['description', 'image'], 'string'],
            [['reference'], 'string', 'max' => 191],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['incoming_id'], 'exist', 'skipOnError' => true, 'targetClass' => Incoming::className(), 'targetAttribute' => ['incoming_id' => 'id']],
            [['payment_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentType::className(), 'targetAttribute' => ['payment_type_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'incoming_id' => 'Incoming',
            'date' => 'Date',
            'amount' => 'Amount',
            'payment_type_id' => 'Payment Type',
            'reference' => 'Reference',
            'description' => 'Description',
            'image' => 'Image',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
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
    public function getIncoming()
    {
        return $this->hasOne(Incoming::className(), ['id' => 'incoming_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentType()
    {
        return $this->hasOne(PaymentType::className(), ['id' => 'payment_type_id']);
    }
}
