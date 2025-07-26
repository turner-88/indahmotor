<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "outgoing".
 *
 * @property integer $id
 * @property string $serial
 * @property string $date
 * @property string $due_date
 * @property integer $outgoing_type_id
 * @property integer $customer_id
 * @property integer $storage_id
 * @property integer $supplier_id
 * @property integer $incoming_item_id
 * @property integer $salesman_id
 * @property string $remark
 * @property double $total
 * @property double $total_payment
 * @property int $payment_status
 * @property integer $is_deleted
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property OutgoingType $outgoingType
 * @property Customer $customer
 * @property Storage $storage
 * @property Supplier $supplier
 * @property IncomingItem $incomingItem
 * @property Salesman $salesman
 * @property OutgoingItem[] $outgoingItems
 * @property Item[] $items
 * @property OutgoingPayment[] $outgoingPayments
 */
class Outgoing extends \yii\db\ActiveRecord
{
    CONST PAYMENT_NONE    = 1;
    CONST PAYMENT_PARTIAL = 2;
    CONST PAYMENT_ALL     = 3;

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
        return 'outgoing';
    }

    public function setTotal()
    {
        $this->total = $this->hasMany(OutgoingItem::className(), ['outgoing_id' => 'id'])->sum('subtotal');
        $this->count_of_items = count($this->outgoingItems);
        return $this->save() ? true : false;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['serial', 'date', 'outgoing_type_id'], 'required'],
            [['date', 'due_date'], 'safe'],
            [['outgoing_type_id', 'customer_id', 'storage_id', 'supplier_id', 'incoming_item_id', 'salesman_id', 'payment_status', 'is_unlimited', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['remark'], 'string'],
            [['total', 'count_of_items'], 'number'],
            [['serial'], 'string', 'max' => 191],
            // [['serial'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['outgoing_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => OutgoingType::className(), 'targetAttribute' => ['outgoing_type_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['storage_id'], 'exist', 'skipOnError' => true, 'targetClass' => Storage::className(), 'targetAttribute' => ['storage_id' => 'id']],
            [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
            [['incoming_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => IncomingItem::className(), 'targetAttribute' => ['incoming_item_id' => 'id']],
            [['salesman_id'], 'exist', 'skipOnError' => true, 'targetClass' => Salesman::className(), 'targetAttribute' => ['salesman_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'No. Faktur',
            'serial' => 'Serial',
            'date' => 'Tanggal',
            'due_date' => 'Jth Tempo',
            'outgoing_type_id' => 'Outgoing Type',
            'customer_id' => 'Pelanggan',
            'storage_id' => 'Storage',
            'supplier_id' => 'Supplier',
            'incoming_item_id' => 'Incoming Item',
            'salesman_id' => 'Salesman',
            'remark' => 'Keterangan',
            'total' => 'Total',
            'count_of_items' => 'Jml Brg',
            'total_payment' => 'Jumlah Sudah Dibayar',
            'payment_status' => 'Status Pembayaran',
            'is_unlimited' => 'Is Unlimited',
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
    public function getOutgoingType()
    {
        return $this->hasOne(OutgoingType::className(), ['id' => 'outgoing_type_id']);
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
    public function getStorage()
    {
        return $this->hasOne(Storage::className(), ['id' => 'storage_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomingItem()
    {
        return $this->hasOne(IncomingItem::className(), ['id' => 'incoming_item_id']);
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
    public function getOutgoingItems()
    {
        return $this->hasMany(OutgoingItem::className(), ['outgoing_id' => 'id'])->joinWith(['item'])->orderBy('box_number ASC, item.name ASC');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(Item::className(), ['id' => 'item_id'])->viaTable('outgoing_item', ['outgoing_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutgoingPayments()
    {
        return $this->hasMany(OutgoingPayment::className(), ['outgoing_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payment::className(), ['outgoing_id' => 'id']);
    }

    public function getIdText()
    {
        return str_pad($this->id, 4, "0", STR_PAD_LEFT);
    }

    public function getShortText()
    {
        return str_pad($this->id, 4, "0", STR_PAD_LEFT).' - '.$this->customer->name;
    }

    public function getShortTextWithRemaining()
    {
        return str_pad($this->id, 4, "0", STR_PAD_LEFT).' - '.$this->customer->name.' - Rp '.Yii::$app->formatter->asInteger($this->getTotalRemaining()).'';
    }

    public static function paymentStatuses($payment_status = 'all')
    {
        $array = [
            self::PAYMENT_NONE      => 'Belum Dibayar',
            self::PAYMENT_PARTIAL   => 'Dibayar Sebagian',
            self::PAYMENT_ALL       => 'Lunas',
        ]; 
        if (isset($array[$payment_status])) return $array[$payment_status];
        if ($payment_status === 'all') return $array;
        return null;
    }

    public static function paymentStatusesHtml($payment_status = 'all')
    {
        $array = [
            self::PAYMENT_NONE      => '<span class="text-bold text-danger">Belum Dibayar</span>',
            self::PAYMENT_PARTIAL   => '<span class="text-bold text-warning">Dibayar Sebagian</span>',
            self::PAYMENT_ALL       => '<span class="text-bold text-success">Lunas</span>',
        ]; 
        if (isset($array[$payment_status])) return $array[$payment_status];
        if ($payment_status === 'all') return $array;
        return null;
    }

    public function getPaymentStatusText()
    {
        return self::paymentStatuses($this->payment_status);
    }

    public function getPaymentStatusHtml()
    {
        return self::paymentStatusesHtml($this->payment_status);
    }

    public function getTotalRemaining()
    {
        return $this->total - $this->total_payment;
    }

    public function getModal()
    {
        $modal = 0;
        foreach ($this->outgoingItems as $outgoingItem) {
            $price = $outgoingItem->incomingItemBefore ? $outgoingItem->incomingItemBefore->price : $outgoingItem->price;
            $modal+= $price * $outgoingItem->quantity;
        }
        return $modal;
    }
}
