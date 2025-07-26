<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\IncomingItem;

/**
 * IncomingItemSearch represents the model behind the search form about `backend\models\IncomingItem`.
 */
class IncomingItemSearch extends IncomingItem
{
    public $incoming_serial;
    public $incoming_date;
    public $incoming_total;
    public $incoming_due_date;
    public $supplier_name;

    public $item_shortcode;
    public $item_brand;
    public $item_type;
    public $item_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'incoming_id', 'item_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['quantity', 'price', 'subtotal', 'discount', 'gross_price'], 'number'],
            [['incoming_serial', 'incoming_date', 'incoming_total', 'incoming_due_date', 'supplier_name'], 'safe'],
            [['item_shortcode', 'item_brand', 'item_type', 'item_name'], 'safe'],
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
        $query = IncomingItem::find();
        $query->joinWith(['incoming.supplier', 'item']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $dataProvider->sort->attributes['incoming_serial'] = [
            'asc' => ['incoming.serial' => SORT_ASC],
            'desc' => ['incoming.serial' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['incoming_date'] = [
            'asc' => ['incoming.date' => SORT_ASC],
            'desc' => ['incoming.date' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['incoming_total'] = [
            'asc' => ['incoming.total' => SORT_ASC],
            'desc' => ['incoming.total' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['incoming_due_date'] = [
            'asc' => ['incoming.due_date' => SORT_ASC],
            'desc' => ['incoming.due_date' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['supplier_name'] = [
            'asc' => ['supplier.name' => SORT_ASC],
            'desc' => ['supplier.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['item_shortcode'] = [
            'asc' => ['item.shortcode' => SORT_ASC],
            'desc' => ['item.shortcode' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['item_brand'] = [
            'asc' => ['item.brand' => SORT_ASC],
            'desc' => ['item.brand' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['item_type'] = [
            'asc' => ['item.type' => SORT_ASC],
            'desc' => ['item.type' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['item_name'] = [
            'asc' => ['item.name' => SORT_ASC],
            'desc' => ['item.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'incoming_item.id' => $this->id,
            'incoming_id' => $this->incoming_id,
            'item_id' => $this->item_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'gross_price' => $this->gross_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'incoming.date' => $this->incoming_date,
            'incoming.total' => $this->incoming_total,
            'incoming.due_date' => $this->incoming_due_date,
        ]);

        $query->andFilterWhere(['like', 'incoming.serial', $this->incoming_serial])
            ->andFilterWhere(['like', 'supplier.name', $this->supplier_name])
            ->andFilterWhere(['like', 'item.shortcode', $this->item_shortcode])
            ->andFilterWhere(['like', 'item.brand', $this->item_brand])
            ->andFilterWhere(['like', 'item.type', $this->item_type])
            ->andFilterWhere(['like', 'item.name', $this->item_name])
        ;

        return $dataProvider;
    }
}
