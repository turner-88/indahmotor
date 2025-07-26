<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ViewItemPrice;

/**
 * ViewItemPriceSearch represents the model behind the search form about `backend\models\ViewItemPrice`.
 */
class ViewItemPriceSearch extends ViewItemPrice
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'harga_list', 'harga_net'], 'integer'],
            [['nama_barang', 'kode_barang', 'merk', 'tipe', 'satuan', 'lokasi_penyimpanan'], 'safe'],
            [['stok', 'diskon_pembelian', 'diskon_A', 'harga_A', 'diskon_B', 'harga_B', 'diskon_C', 'harga_C'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ViewItemPrice::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'stok' => $this->stok,
            'harga_list' => $this->harga_list,
            'diskon_pembelian' => $this->diskon_pembelian,
            'harga_net' => $this->harga_net,
            'diskon_A' => $this->diskon_A,
            'harga_A' => $this->harga_A,
            'diskon_B' => $this->diskon_B,
            'harga_B' => $this->harga_B,
            'diskon_C' => $this->diskon_C,
            'harga_C' => $this->harga_C,
        ]);

        $query->andFilterWhere(['like', 'nama_barang', $this->nama_barang])
            ->andFilterWhere(['like', 'kode_barang', $this->kode_barang])
            ->andFilterWhere(['like', 'merk', $this->merk])
            ->andFilterWhere(['like', 'tipe', $this->tipe])
            ->andFilterWhere(['like', 'satuan', $this->satuan])
            ->andFilterWhere(['like', 'lokasi_penyimpanan', $this->lokasi_penyimpanan]);

        return $dataProvider;
    }
}
