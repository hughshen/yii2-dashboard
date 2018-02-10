<?php

namespace backend\modules\cms\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use backend\modules\cms\models\Post;

/**
 * PostSearch represents the model behind the search form about `backend\modules\cms\models\Post`.
 */
class PostSearch extends Post
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['slug', 'title', 'excerpt', 'status'], 'string'],
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
        $query = Post::find()
            ->andWhere(['type' => Post::typeName()])
            ->andWhere(['!=', 'status', Post::STATUS_TRASH]);

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

        $query->andFilterWhere(['like', 'slug', $this->slug]);
        $query->andFilterWhere(['like', 'status', $this->status]);

        // Translate
        $query = Post::leftJoinTranslate($query);
        $query = Post::fieldFilterTranslate($query, 'title', $this->title);
        $query = Post::fieldFilterTranslate($query, 'excerpt', $this->excerpt);

        return $dataProvider;
    }
}
