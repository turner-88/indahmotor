<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\OutgoingItem;

/**
 * OutgoingItemSearch represents the model behind the search form about `backend\models\OutgoingItem`.
 */
class OutgoingItemSearch extends OutgoingItem
{
    public $outgoing_serial;
    public $outgoing_date;
    public $outgoing_total;
    public $outgoing_due_date;
    public $customer_name;
    public $salesman_name;

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
            [['id', 'outgoing_id', 'item_id', 'discount', 'is_taxable', 'box_number', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['quantity', 'price', 'adjustment', 'subtotal'], 'number'],
            [['outgoing_serial', 'outgoing_date', 'outgoing_total', 'outgoing_due_date', 'customer_name', 'salesman_name'], 'safe'],
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
        $query = OutgoingItem::find();
        $query->joinWith(['outgoing.customer', 'outgoing.salesman', 'item']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $dataProvider->sort->attributes['outgoing_serial'] = [
            'asc' => ['outgoing.serial' => SORT_ASC],
            'desc' => ['outgoing.serial' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['outgoing_date'] = [
            'asc' => ['outgoing.date' => SORT_ASC],
            'desc' => ['outgoing.date' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['outgoing_total'] = [
            'asc' => ['outgoing.total' => SORT_ASC],
            'desc' => ['outgoing.total' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['outgoing_due_date'] = [
            'asc' => ['outgoing.due_date' => SORT_ASC],
            'desc' => ['outgoing.due_date' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['customer_name'] = [
            'asc' => ['customer.name' => SORT_ASC],
            'desc' => ['customer.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['salesman_name'] = [
            'asc' => ['salesman.name' => SORT_ASC],
            'desc' => ['salesman.name' => SORT_DESC],
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
            'id' => $this->id,
            'outgoing_id' => $this->outgoing_id,
            'item_id' => $this->item_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'discount' => $this->discount,
            'is_taxable' => $this->is_taxable,
            'adjustment' => $this->adjustment,
            'subtotal' => $this->subtotal,
            'box_number' => $this->box_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'outgoing.date' => $this->outgoing_date,
            'outgoing.total' => $this->outgoing_total,
            'outgoing.due_date' => $this->outgoing_due_date,
        ]);

        $query->andFilterWhere(['like', 'outgoing.serial', $this->outgoing_serial])
            ->andFilterWhere(['like', 'customer.name', $this->customer_name])
            ->andFilterWhere(['like', 'salesman.name', $this->salesman_name])
            ->andFilterWhere(['like', 'item.shortcode', $this->item_shortcode])
            ->andFilterWhere(['like', 'item.brand', $this->item_brand])
            ->andFilterWhere(['like', 'item.type', $this->item_type])
            ->andFilterWhere(['like', 'item.name', $this->item_name])
        ;

        return $dataProvider;
    }
}
