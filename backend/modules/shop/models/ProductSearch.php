<?php

namespace backend\modules\shop\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProductSearch represents the model behind the search form of `backend\modules\shop\models\Product`.
 */
class ProductSearch extends Product
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['slug', 'title', 'status'], 'string'],
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
        $query = Product::find()
            ->andWhere(['=', 'deleted_at', 0])
            ->groupBy('id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sorting' => SORT_ASC,
                    'id' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageSize' => Yii::$app->params['tablePageSize'],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'slug', $this->slug]);
        $query->andFilterWhere(['status' => $this->status]);

        // Translate
        $query = Product::leftJoinTranslate($query);
        $query = Product::fieldFilterTranslate($query, 'title', $this->title);

        return $dataProvider;
    }
}