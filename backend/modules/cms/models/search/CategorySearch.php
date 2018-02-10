<?php

namespace backend\modules\cms\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use backend\modules\cms\models\Category;

/**
 * CategorySearch represents the model behind the search form about `backend\modules\cms\models\Category`.
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
            ->with('parentCategory')
            ->andWhere(['type' => Category::typeName()]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sorting' => SORT_ASC,
                    'id' => SORT_DESC,
                ],
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
