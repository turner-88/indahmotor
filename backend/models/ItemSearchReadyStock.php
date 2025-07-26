<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Item;

/**
 * ItemSearch represents the model behind the search form about `backend\models\Item`.
 */
class ItemSearchReadyStock extends Item
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name', 'shortcode', 'brand', 'type', 'unit_of_measurement'], 'safe'],
            [['current_quantity', 'purchase_net_price', 'purchase_gross_price', 'purchase_discount'], 'number'],
            [['prices'], 'safe'],
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
        $query = Item::find()->where(['>', 'current_quantity', 0]);

        // add conditions that should always apply here

        $query->joinWith(['itemPrices']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['shortcode' => SORT_ASC]],
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
            'current_quantity' => $this->current_quantity,
            'purchase_net_price' => $this->purchase_net_price,
            'purchase_gross_price' => $this->purchase_gross_price,
            'purchase_discount' => $this->purchase_discount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'shortcode', $this->shortcode. '%', false])
            ->andFilterWhere(['like', 'brand', $this->brand])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'unit_of_measurement', $this->unit_of_measurement])
            ->andFilterWhere([
                'like',
                '(select (group_concat(price)) as prices from item_price 
                    where item_price.item_id = item.id
                )',
                $this->getAttribute('prices')
            ]);

        $query->groupBy('item.id');

        return $dataProvider;
    }

    function attributes()
    {
        return array_merge(parent::attributes(), [
            'prices',
        ]);
    }
}
