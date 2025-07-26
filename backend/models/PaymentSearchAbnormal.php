<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Payment;

/**
 * PaymentSearch represents the model behind the search form about `backend\models\Payment`.
 */
class PaymentSearchAbnormal extends Payment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'incoming_id', 'supplier_id', 'outgoing_id', 'customer_id', 'amount', 'adjustment', 'return', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['date', 'remark'], 'safe'],
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
        $query = Payment::find();

        // add conditions that should always apply here
        $query->where(['and', 
            ['is not', 'supplier_id', null],
            ['is not', 'customer_id', null],
        ]);

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
            'supplier_id' => $this->supplier_id,
            'customer_id' => $this->customer_id,
            'date' => $this->date,
            'amount' => $this->amount,
            'adjustment' => $this->adjustment,
            'return' => $this->return,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'remark', $this->remark]);
        $query->andFilterWhere(['like', 'outgoing_id', $this->outgoing_id]);
        
        if ($this->incoming_id) {
            $query->joinWith(['incoming']);
            $query->andFilterWhere([
                'or',
                ['like', 'incoming_id', $this->incoming_id],
                ['like', 'incoming.serial', $this->incoming_id],
            ]);
        }

        return $dataProvider;
    }
}
