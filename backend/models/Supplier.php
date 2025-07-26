<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "supplier".
 *
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property string $phone
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property Incoming[] $incomings
 * @property Outgoing[] $outgoings
 * @property User $createdBy
 * @property User $updatedBy
 */
class Supplier extends \yii\db\ActiveRecord
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
        return 'supplier';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['address'], 'string'],
            [['created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name', 'phone'], 'string', 'max' => 191],
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
            'phone' => 'Phone',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomings()
    {
        return $this->hasMany(Incoming::className(), ['supplier_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutgoings()
    {
        return $this->hasMany(Outgoing::className(), ['supplier_id' => 'id']);
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

    public function getShortText()
    {
        return $this->name . ' (' .$this->address . ')';
    }

    public function setPaymentStatus()
    {
        // reset payment_status
        Yii::$app->db->createCommand("update incoming set total_payment = 0, payment_status = ".Incoming::PAYMENT_NONE." where supplier_id = '".$this->id."'")->execute();

        // set defined incoming
        $overpayment = 0; 
        $incomingPaids = Incoming::find()->joinWith(['payments'])->where(['incoming.supplier_id' => $this->id])->andWhere(['is not', 'incoming_id', null])->groupBy('incoming.id')->all();
        foreach ($incomingPaids as $incomingPaid) {
            $incoming_paid_sum = Payment::find()->where(['incoming_id' => $incomingPaid->id])->sum(new \yii\db\Expression('coalesce(amount, 0) + coalesce(adjustment, 0) + coalesce(`return`, 0)'));
            // dd($incoming_paid_sum);
            if ($incoming_paid_sum >= $incomingPaid->total) {
                $overpayment+= $incoming_paid_sum - $incomingPaid->total;
                $incomingPaid->payment_status = Incoming::PAYMENT_ALL;
            }
            if ($incoming_paid_sum < $incomingPaid->total) $incomingPaid->payment_status = Incoming::PAYMENT_PARTIAL;
            if ($incoming_paid_sum == 0) $incomingPaid->payment_status = Incoming::PAYMENT_NONE;
			$incomingPaid->total_payment = min($incoming_paid_sum, $incomingPaid->total);
            $incomingPaid->save();
        }

        // set undefined incoming
        $payment_undefined = Payment::find()->where(['supplier_id' => $this->id])->andWhere(['incoming_id' => null])->sum(new \yii\db\Expression('coalesce(amount, 0) + coalesce(adjustment, 0) + coalesce(`return`, 0)'));
        $payment_undefined+= $overpayment;
        
        $incomingUnpaids = Incoming::find()->joinWith(['payments'])->where(['incoming.supplier_id' => $this->id])->andWhere(['incoming_id' => null])->groupBy('incoming.id')->orderBy('id')->all();
        foreach ($incomingUnpaids as $incomingUnpaid) {
            if ($payment_undefined > 0) {
                if ($payment_undefined >= $incomingUnpaid->total) $incomingUnpaid->payment_status = Incoming::PAYMENT_ALL;
                if ($payment_undefined < $incomingUnpaid->total) $incomingUnpaid->payment_status = Incoming::PAYMENT_PARTIAL;
                if ($payment_undefined == 0) $incomingUnpaid->payment_status = Incoming::PAYMENT_NONE;
				$incomingUnpaid->total_payment = min($payment_undefined, $incomingUnpaid->total);
                $incomingUnpaid->save();
                $payment_undefined -= min($incomingUnpaid->total, $payment_undefined);
            }
        }

        // old code
        /* $incoming_total = Incoming::find()->where(['supplier_id' => $this->id])->sum('total');
        $payment = 0;
        $payment+= Payment::find()->where(['supplier_id' => $this->id])->sum('amount');
        $payment+= Payment::find()->where(['supplier_id' => $this->id])->sum('adjustment');
        $payment+= Payment::find()->where(['supplier_id' => $this->id])->sum('`return`');

        $credit = $incoming_total - $payment;

        $last_unpaid_id = null;
        $credit_remaining = $credit;
        $incomings = Incoming::find()->where(['supplier_id' => $this->id])->orderBy('id DESC')->all();

        foreach ($incomings as $incoming) {
            if ($credit_remaining) {
                $incoming->total_payment = ($credit_remaining >= $incoming->total) ? 0 : ($incoming->total - $credit_remaining);
                $incoming->payment_status = $incoming->total_payment ? 2 : 1;
                $incoming->save();
                $last_unpaid_id = $incoming->id;
            }
            $credit_remaining-= $incoming->total;
            if ($credit_remaining <= 0) break;
        }
        if ($last_unpaid_id) Yii::$app->db->createCommand("update incoming set total_payment = incoming.total, payment_status = 3 where supplier_id = '".$this->id."' and id < ".$last_unpaid_id)->execute();
        else Yii::$app->db->createCommand("update incoming set total_payment = incoming.total, payment_status = 3 where supplier_id = '".$this->id."'")->execute();
        return; */
    }
}
