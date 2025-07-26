<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Outgoing;

/**
 * OutgoingSearch represents the model behind the search form about `backend\models\Outgoing`.
 */
class OutgoingSearch extends Outgoing
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'outgoing_type_id', 'customer_id', 'storage_id', 'supplier_id', 'incoming_item_id', 'salesman_id', 'payment_status', 'is_unlimited', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['serial', 'date', 'due_date', 'remark'], 'safe'],
            [['total', 'count_of_items', 'total_payment'], 'number'],
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
        $query = Outgoing::find();

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
            'date' => $this->date,
            'due_date' => $this->due_date,
            'outgoing_type_id' => $this->outgoing_type_id,
            'customer_id' => $this->customer_id,
            'storage_id' => $this->storage_id,
            'supplier_id' => $this->supplier_id,
            'incoming_item_id' => $this->incoming_item_id,
            'salesman_id' => $this->salesman_id,
            'total' => $this->total,
            'count_of_items' => $this->count_of_items,
            'total_payment' => $this->total_payment,
            'payment_status' => $this->payment_status,
            'is_unlimited' => $this->is_unlimited,
            'is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'serial', $this->serial])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
