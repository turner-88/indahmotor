<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "incoming".
 *
 * @property integer $id
 * @property string $serial
 * @property string $date
 * @property string $due_date
 * @property integer $incoming_type_id
 * @property integer $supplier_id
 * @property integer $storage_id
 * @property integer $customer_id
 * @property integer $outgoing_item_id
 * @property integer $salesman_id
 * @property string $remark
 * @property double $total
 * @property integer $is_deleted
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property IncomingType $incomingType
 * @property Supplier $supplier
 * @property Storage $storage
 * @property Customer $customer
 * @property OutgoingItem $outgoingItem
 * @property Salesman $salesman
 * @property IncomingItem[] $incomingItems
 * @property Item[] $items
 * @property IncomingPayment[] $incomingPayments
 */
class Incoming extends \yii\db\ActiveRecord
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
        return 'incoming';
    }

    public function setTotal()
    {
        $this->total = IncomingItem::find()->where(['incoming_id' => $this->id])->sum('subtotal');
        $this->count_of_items = count($this->incomingItems);
        return $this->save() ? true : false;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'incoming_type_id', 'supplier_id'], 'required'],
            [['date', 'due_date'], 'safe'],
            [['incoming_type_id', 'supplier_id', 'storage_id', 'customer_id', 'outgoing_item_id', 'salesman_id', 'payment_status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['remark'], 'string'],
            [['total', 'count_of_items'], 'number'],
            [['serial'], 'string', 'max' => 191],
            [['serial'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['incoming_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => IncomingType::className(), 'targetAttribute' => ['incoming_type_id' => 'id']],
            [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
            [['storage_id'], 'exist', 'skipOnError' => true, 'targetClass' => Storage::className(), 'targetAttribute' => ['storage_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['outgoing_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => OutgoingItem::className(), 'targetAttribute' => ['outgoing_item_id' => 'id']],
            [['salesman_id'], 'exist', 'skipOnError' => true, 'targetClass' => Salesman::className(), 'targetAttribute' => ['salesman_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Faktur Pembelian',
            'serial' => 'Faktur Distributor',
            'date' => 'Tanggal',
            'due_date' => 'Jth Tempo',
            'incoming_type_id' => 'Incoming Type',
            'supplier_id' => 'Distributor',
            'storage_id' => 'Storage',
            'customer_id' => 'Customer',
            'outgoing_item_id' => 'Outgoing Item',
            'salesman_id' => 'Salesman',
            'remark' => 'Keterangan',
            'total' => 'Total',
            'count_of_items' => 'Jml Brg',
            'total_payment' => 'Jumlah Sudah Dibayar',
            'payment_status' => 'Status Pembayaran',
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
    public function getIncomingType()
    {
        return $this->hasOne(IncomingType::className(), ['id' => 'incoming_type_id']);
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
    public function getStorage()
    {
        return $this->hasOne(Storage::className(), ['id' => 'storage_id']);
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
    public function getOutgoingItem()
    {
        return $this->hasOne(OutgoingItem::className(), ['id' => 'outgoing_item_id']);
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
    public function getIncomingItems()
    {
        return $this->hasMany(IncomingItem::className(), ['incoming_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomingItemsAlphabetical()
    {
        return $this->hasMany(IncomingItem::className(), ['incoming_id' => 'id'])->joinWith(['item'])->orderBy('item.name ASC');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(Item::className(), ['id' => 'item_id'])->viaTable('incoming_item', ['incoming_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomingPayments()
    {
        return $this->hasMany(IncomingPayment::className(), ['incoming_id' => 'id']);
    }

    public function getIdText()
    {
        return str_pad($this->id, 4, "0", STR_PAD_LEFT);
    }

    public function getShortText()
    {
        return str_pad($this->id, 4, "0", STR_PAD_LEFT).($this->serial ? ' - '.$this->serial : ''). ' - '.$this->supplier->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payment::className(), ['incoming_id' => 'id']);
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
}
