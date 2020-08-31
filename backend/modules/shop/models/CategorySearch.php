<?php

namespace backend\modules\shop\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CategorySearch represents the model behind the search form about `backend\modules\shop\models\Category`.
 */
class CategorySearch extends Category
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['parent', 'integer'],
            [['slug', 'title', 'description'], 'safe'],
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
        $query = Category::find()
            ->with('prevCat')
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

        $query->andFilterWhere(['parent' => $this->parent]);
        $query->andFilterWhere(['like', 'slug', $this->slug]);

        // Translate
        $query = Category::leftJoinTranslate($query);
        $query = Category::fieldFilterTranslate($query, 'title', $this->title);

        return $dataProvider;
    }
}