<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "_view_item_price".
 *
 * @property int $id
 * @property string $nama_barang
 * @property string $kode_barang
 * @property string $merk
 * @property string $tipe
 * @property string $satuan
 * @property double $stok
 * @property string $lokasi_penyimpanan
 * @property int $harga_list
 * @property double $diskon_pembelian
 * @property int $harga_net
 * @property double $diskon_A
 * @property string $harga_A
 * @property double $diskon_B
 * @property string $harga_B
 * @property double $diskon_C
 * @property string $harga_C
 */
class ViewItemPrice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '_view_item_price';
    }
    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'harga_list', 'harga_net'], 'integer'],
            [['nama_barang'], 'required'],
            [['stok', 'diskon_pembelian', 'diskon_A', 'harga_A', 'diskon_B', 'harga_B', 'diskon_C', 'harga_C'], 'number'],
            [['nama_barang', 'kode_barang', 'merk', 'tipe', 'satuan', 'lokasi_penyimpanan'], 'string', 'max' => 191],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nama_barang' => 'Nama Barang',
            'kode_barang' => 'Kode Barang',
            'merk' => 'Merk',
            'tipe' => 'Tipe',
            'satuan' => 'Satuan',
            'stok' => 'Stok',
            'lokasi_penyimpanan' => 'Lokasi Penyimpanan',
            'harga_list' => 'Harga List',
            'diskon_pembelian' => 'Diskon Pembelian',
            'harga_net' => 'Harga Net',
            'diskon_A' => 'Diskon A',
            'harga_A' => 'Harga A',
            'diskon_B' => 'Diskon B',
            'harga_B' => 'Harga B',
            'diskon_C' => 'Diskon C',
            'harga_C' => 'Harga C',
        ];
    }
}
