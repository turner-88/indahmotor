<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "expense".
 *
 * @property integer $id
 * @property string $name
 * @property string $date
 * @property integer $amount
 * @property string $person_in_charge
 * @property integer $salesman_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property Salesman $salesman
 */
class Expense extends \yii\db\ActiveRecord
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
        return 'expense';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'date', 'amount'], 'required'],
            [['date'], 'safe'],
            [['amount', 'salesman_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name', 'person_in_charge'], 'string', 'max' => 191],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['salesman_id'], 'exist', 'skipOnError' => true, 'targetClass' => Salesman::className(), 'targetAttribute' => ['salesman_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Keterangan',
            'date' => 'Tanggal',
            'amount' => 'Jumlah',
            'person_in_charge' => 'Penanggung Jawab',
            'salesman_id' => 'Salesman',
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
    public function getSalesman()
    {
        return $this->hasOne(Salesman::className(), ['id' => 'salesman_id']);
    }
}
