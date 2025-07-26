<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\OrderItem;

/**
 * OrderItemSearch represents the model behind the search form about `backend\models\OrderItem`.
 */
class OrderItemSearch extends OrderItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'quantity', 'to_be_ordered', 'supplier_id', 'item_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['item_name', 'item_shortcode', 'brand_storage', 'brand_supplier', 'type', 'unit_of_measurement'], 'safe'],
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
        $query = OrderItem::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
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
            'order_id' => $this->order_id,
            'quantity' => $this->quantity,
            'to_be_ordered' => $this->to_be_ordered,
            'supplier_id' => $this->supplier_id,
            'item_id' => $this->item_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'item_name', $this->item_name])
            ->andFilterWhere(['like', 'item_shortcode', $this->item_shortcode])
            ->andFilterWhere(['like', 'brand_storage', $this->brand_storage])
            ->andFilterWhere(['like', 'brand_supplier', $this->brand_supplier])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'unit_of_measurement', $this->unit_of_measurement]);

        return $dataProvider;
    }
}
