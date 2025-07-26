<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "salesman".
 *
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property string $date_of_birth
 * @property string $phone
 * @property string $identity_number
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property Customer[] $customers
 * @property Incoming[] $incomings
 * @property Outgoing[] $outgoings
 * @property User $createdBy
 * @property User $updatedBy
 */
class Salesman extends \yii\db\ActiveRecord
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
        return 'salesman';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['address'], 'string'],
            [['date_of_birth'], 'safe'],
            [['created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name', 'phone', 'identity_number'], 'string', 'max' => 191],
            [['name'], 'unique'],
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
            'address' => 'Address',
            'date_of_birth' => 'Date Of Birth',
            'phone' => 'Phone',
            'identity_number' => 'Identity Number',
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
        return $this->hasMany(Customer::className(), ['salesman_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomings()
    {
        return $this->hasMany(Incoming::className(), ['salesman_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutgoings()
    {
        return $this->hasMany(Outgoing::className(), ['salesman_id' => 'id']);
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
