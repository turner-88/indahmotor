<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Expense;

/**
 * ExpenseSearch represents the model behind the search form about `backend\models\Expense`.
 */
class ExpenseSearch extends Expense
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'amount', 'salesman_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name', 'date', 'person_in_charge'], 'safe'],
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
        $query = Expense::find();

        // add conditions that should always apply here
        if (Yii::$app->user->can('salesman')) {
            $query->where(['created_by' => Yii::$app->user->id]);
        }

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
            'amount' => $this->amount,
            'salesman_id' => $this->salesman_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'person_in_charge', $this->person_in_charge]);

        return $dataProvider;
    }
}
