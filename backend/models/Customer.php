<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property string $phone
 * @property string $person_in_charge
 * @property integer $price_group_id
 * @property integer $salesman_id
 * @property integer $payment_limit_duration
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property Salesman $salesman
 * @property PriceGroup $priceGroup
 * @property Incoming[] $incomings
 * @property Outgoing[] $outgoings
 */
class Customer extends \yii\db\ActiveRecord
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
        return 'customer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['address'], 'string'],
            [['price_group_id', 'salesman_id', 'payment_limit_duration', 'initial_debt', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name', 'phone', 'person_in_charge'], 'string', 'max' => 191],
            [['name'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['salesman_id'], 'exist', 'skipOnError' => true, 'targetClass' => Salesman::className(), 'targetAttribute' => ['salesman_id' => 'id']],
            [['price_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => PriceGroup::className(), 'targetAttribute' => ['price_group_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nama',
            'address' => 'Alamat',
            'phone' => 'Telp',
            'person_in_charge' => 'Owner/CP',
            'price_group_id' => 'Kelompok Harga',
            'salesman_id' => 'Salesman',
            'payment_limit_duration' => 'Jatuh Tempo',
            'initial_debt' => 'Piutang Awal',
            'debt' => 'Piutang Saat Ini',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceGroup()
    {
        return $this->hasOne(PriceGroup::className(), ['id' => 'price_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomings()
    {
        return $this->hasMany(Incoming::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutgoings()
    {
        return $this->hasMany(Outgoing::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payment::className(), ['customer_id' => 'id']);
    }

    public function getShortText()
    {
        return $this->name . ' (' . ucwords(strtolower($this->address)) . ')';
    }

    public function getShortTextWithDebt()
    {
        return $this->name . ' (' . ucwords(strtolower($this->address)) . ') - Rp ' . Yii::$app->formatter->asDecimal($this->debt, 0);
    }

    public function getDebt() {
        $total_outgoing = Outgoing::find()->where(['customer_id' => $this->id])->sum('total');
        $total_payment1  = Payment::find()->where(['customer_id' => $this->id])->sum('amount');
        $total_payment2  = Payment::find()->where(['customer_id' => $this->id])->sum('adjustment');
        $total_payment3  = Payment::find()->where(['customer_id' => $this->id])->sum('`return`');
        $total_payment  = $total_payment1 + $total_payment2 + $total_payment3;
        return $total_outgoing - $total_payment + $this->initial_debt;
    }

    public function setPaymentStatus()
    {
        // reset payment_status
        Yii::$app->db->createCommand("update outgoing set total_payment = 0, payment_status = ".Outgoing::PAYMENT_NONE." where customer_id = '".$this->id."'")->execute();

        // set defined outgoing
        $overpayment = 0; 
        $outgoingPaids = Outgoing::find()->joinWith(['payments'])->where(['outgoing.customer_id' => $this->id])->andWhere(['is not', 'outgoing_id', null])->groupBy('outgoing.id')->all();
        foreach ($outgoingPaids as $outgoingPaid) {
            $outgoing_paid_sum = Payment::find()->where(['outgoing_id' => $outgoingPaid->id])->sum(new \yii\db\Expression('coalesce(amount, 0) + coalesce(adjustment, 0) + coalesce(`return`, 0)'));
            // dd($outgoing_paid_sum);
            if ($outgoing_paid_sum >= $outgoingPaid->total) {
                $overpayment+= $outgoing_paid_sum - $outgoingPaid->total;
                $outgoingPaid->payment_status = Outgoing::PAYMENT_ALL;
            }
            if ($outgoing_paid_sum < $outgoingPaid->total) $outgoingPaid->payment_status = Outgoing::PAYMENT_PARTIAL;
            if ($outgoing_paid_sum == 0) $outgoingPaid->payment_status = Outgoing::PAYMENT_NONE;
			$outgoingPaid->total_payment = min($outgoing_paid_sum, $outgoingPaid->total);
            $outgoingPaid->save();
        }

        // set undefined outgoing
        $payment_undefined = Payment::find()->where(['customer_id' => $this->id])->andWhere(['outgoing_id' => null])->sum(new \yii\db\Expression('coalesce(amount, 0) + coalesce(adjustment, 0) + coalesce(`return`, 0)'));
        $payment_undefined+= $overpayment;
        
        $outgoingUnpaids = Outgoing::find()->joinWith(['payments'])->where(['outgoing.customer_id' => $this->id])->andWhere(['outgoing_id' => null])->groupBy('outgoing.id')->orderBy('id')->all();
        foreach ($outgoingUnpaids as $outgoingUnpaid) {
            if ($payment_undefined > 0) {
                if ($payment_undefined >= $outgoingUnpaid->total) $outgoingUnpaid->payment_status = Outgoing::PAYMENT_ALL;
                if ($payment_undefined < $outgoingUnpaid->total) $outgoingUnpaid->payment_status = Outgoing::PAYMENT_PARTIAL;
                if ($payment_undefined == 0) $outgoingUnpaid->payment_status = Outgoing::PAYMENT_NONE;
				$outgoingUnpaid->total_payment = min($payment_undefined, $outgoingUnpaid->total);
                $outgoingUnpaid->save();
                $payment_undefined -= min($outgoingUnpaid->total, $payment_undefined);
            }
        }
        

        // calculate defined transaction
        // task status: undone
        /* $query = Outgoing::find()->joinWith(['payments'])->where(['payment.customer_id' => $this->id])->andWhere(['is not', 'payment.outgoing_id', null])->andWhere([
            'outgoing.total' => new \yii\db\Expression('
                if(sum(payment.amount) is null, 0, sum(payment.amount))
                + if(sum(payment.adjustment) is null, 0, sum(payment.adjustment))
                + if(sum(payment.return) is null, 0, sum(payment.return))
            ')
        ])->groupBy('outgoing.id');
        dd($query->createCommand()->rawSql);
        $payments = $query->all();
        foreach ($payments as $payment) {
            
        } */


        // calculate undefined transaction
        // old code
        /* $outgoing_total = Outgoing::find()->where(['customer_id' => $this->id])->and(['outgoing_id'])->sum('total');
        $payment = 0;
        $payment+= Payment::find()->where(['customer_id' => $this->id])->andWhere(['outgoing_id' => null])->sum('amount');
        $payment+= Payment::find()->where(['customer_id' => $this->id])->andWhere(['outgoing_id' => null])->sum('adjustment');
        $payment+= Payment::find()->where(['customer_id' => $this->id])->andWhere(['outgoing_id' => null])->sum('`return`');

        $credit = $outgoing_total - $payment + $this->initial_debt;

        $last_unpaid_id = null;
        $credit_remaining = $credit;
        $outgoings = Outgoing::find()->where(['customer_id' => $this->id])->orderBy('id DESC')->all();

        foreach ($outgoings as $outgoing) {
            if ($credit_remaining) {
                $outgoing->total_payment = ($credit_remaining >= $outgoing->total) ? 0 : ($outgoing->total - $credit_remaining);
                $outgoing->payment_status = $outgoing->total_payment ? 2 : 1;
                $outgoing->save();
                $last_unpaid_id = $outgoing->id;
            }
            $credit_remaining-= $outgoing->total;
            if ($credit_remaining <= 0) break;
        }
        if ($last_unpaid_id) Yii::$app->db->createCommand("update outgoing set total_payment = outgoing.total, payment_status = 3 where customer_id = '".$this->id."' and id < ".$last_unpaid_id)->execute();
        else Yii::$app->db->createCommand("update outgoing set total_payment = outgoing.total, payment_status = 3 where customer_id = '".$this->id."'")->execute();
        return; */
    }
}
