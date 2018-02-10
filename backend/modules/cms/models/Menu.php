<?php

namespace backend\modules\cms\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

class Menu extends Category
{
    /**
     * Return type
     */
    public static function typeName()
    {
        return 'menu';
    }

    public function getParentMenu()
    {
        return $this->hasOne(static::className(), ['id' => 'parent'])->with(['translated'])->andOnCondition(['status' => 1, 'type' => static::typeName()]);
    }

    public function getFirstChild()
    {
        return $this->hasOne(self::className(), ['parent' => 'id'])->with(['translated'])->andOnCondition(['status' => 1, 'type' => static::typeName()])->orderBy('sorting ASC, created_at DESC');
    }

    public function extraFields()
    {
        $this->extractExtraData();        
        $menuType = $this->getExtraValue('type');
        $promptText = '----';
        return [
            [
                'fieldName' => 'type',
                'inputLabel' => Yii::t('app', 'Menu Type'),
                'valueData' => $this->extraData,
                'inputType' => 'dropdown',
                'valueList' => self::menuTypeList(),
                'defaultValue' => $menuType,
                'promptText' => $promptText,
            ],
            [
                'fieldName' => 'type_id',
                'inputLabel' => Yii::t('app', 'Menu Type Name'),
                'valueData' => $this->extraData,
                'inputType' => 'dropdown',
                'valueList' => ArrayHelper::map(self::menuIdList($menuType), 'id', 'value'),
                'defaultValue' => $this->getExtraValue('type_id'),
                'promptText' => $promptText,
            ],
            [
                'fieldName' => 'route',
                'inputLabel' => Yii::t('app', 'Menu Route'),
                'valueData' => $this->extraData,
                'defaultValue' => $this->getExtraValue('route'),
            ],
            [
                'fieldName' => 'link',
                'inputLabel' => Yii::t('app', 'Menu Link'),
                'valueData' => $this->extraData,
                'defaultValue' => $this->getExtraValue('link'),
            ],
            [
                'fieldName' => 'target',
                'inputLabel' => Yii::t('app', 'Link Target'),
                'valueData' => $this->extraData,
                'inputType' => 'dropdown',
                'valueList' => [
                    '' => Yii::t('app', 'Current Window'),
                    '_blank' => Yii::t('app', 'New Window'),
                ],
                'defaultValue' => $this->getExtraValue('target'),
            ],
            // [
            //     'fieldName' => 'attributes',
            //     'inputLabel' => Yii::t('app', 'Tag Attributes'),
            //     'valueData' => $this->extraData,
            //     'defaultValue' => $this->getExtraValue('attributes'),
            // ],
            [
                'fieldName' => 'template',
                'inputLabel' => Yii::t('app', 'Menu Template'),
                'valueData' => $this->extraData,
                'inputType' => 'dropdown',
                'valueList' => self::findTemplates(),
                'defaultValue' => $this->getExtraValue('template'),
                'promptText' => $promptText,
            ],
        ];
    }

    public static function menuTypeList()
    {
        return [
            'post' => Yii::t('app', 'Post'),
            'page' => Yii::t('app', 'Page'),
            'tag' => Yii::t('app', 'Tag'),
            'category' => Yii::t('app', 'Category'),
        ];
    }

    public static function findTemplates()
    {
        $templateFolder = Yii::getAlias("@frontend/views/site/menu-templates");

        if (!file_exists($templateFolder)) return [];

        $files = \yii\helpers\FileHelper::findFiles($templateFolder, [
            'only' => ['*.php']
        ]);

        if (!is_array($files)) return [];
        
        $templates = [];
        foreach ($files as $file) {
            $templates[] = basename($file, '.php');
        }

        return array_combine($templates, $templates);
    }

    public static function menuIdList($type = 'post')
    {
        if (!in_array($type, array_keys(self::menuTypeList()))) return [];

        $data = [];
        switch ($type) {
            case 'post':
            case 'page':
                $query = Post::find()
                    ->with(['translated'])
                    ->where(['status' => Post::STATUS_PUBLISH, 'type' => $type])
                    ->asArray()
                    ->all();

                $query = Post::combineTranslatedData($query);

                foreach ($query as $val) {
                    $data[] = [
                        'id' => $val['id'],
                        'value' => $val['title'],
                    ];
                }
                break;
            case 'tag':
            case 'category':
                $query = Category::find()
                    ->with(['translated'])
                    ->where(['status' => 1, 'type' => $type])
                    ->asArray()
                    ->all();

                $query = Category::combineTranslatedData($query);

                foreach ($query as $val) {
                    $data[] = [
                        'id' => $val['id'],
                        'value' => $val['title'],
                    ];
                }
                break;
        }

        return $data;
    }

    public static function commonQuery($condition = [])
    {
        $query = self::find()
            ->with(['translated', 'parentMenu', 'firstChild'])
            ->where(['status' => 1, 'type' => static::typeName()])
            ->andWhere($condition);

        return $query;
    }

    public static function byId($id)
    {
        return self::commonQuery(['id' => $id])->asArray()->one();
    }

    public static function bySlug($slug)
    {
        return self::commonQuery(['slug' => $slug])->asArray()->one();
    }

    public static function buildData($data)
    {
        $selfBuild = ['parentMenu', 'firstChild'];
        foreach ($selfBuild as $val) {
            if (isset($data[$val])) {
                $data[$val] = self::buildData($data[$val]);
            }
        }

        $data = self::combineTranslatedData($data);
        $data['menu_url'] = self::buildUrl($data);

        return $data;
    }

    public static function frontendList()
    {
        $data = self::commonQuery()->orderBy('parent ASC, sorting ASC, created_at DESC')->asArray()->all();

        $newData = [];
        foreach ($data as $val) {
            if ($val['parent'] == 0) {
                if ($val['slug']) {
                    $newData[$val['slug']] = $val['id'];
                }
            } else {
                $newData[$val['parent']][] = self::buildData($val);
            }
        }

        return $newData;
    }

    public static function groupList($group, $menus = null)
    {
        if ($menus === null) {
            $menus = self::frontendList();
        }

        $id = isset($menus[$group]) ? $menus[$group] : null;

        if ($id && isset($menus[$id])) {
            return $menus[$id];
        } else {
            return [];
        }
    }

    public static function buildUrl($menu)
    {
        if (!isset($menu['extra_data'])) return null;

        $extra = json_decode($menu['extra_data'], true);

        $url = '';

        if (isset($extra['route']) && !empty($extra['route'])) {
            $url = '/' . ltrim($extra['route'], '/');
            $url = [$url, 'mid' => $menu['id']];
        } elseif (isset($extra['link']) && !empty($extra['link'])) {
            $url = $extra['link'];
        } elseif (isset($extra['type']) && isset($extra['type_id']) && !empty($extra['type']) && !empty($extra['type_id'])) {
            // $url = ['/site/' . $extra['type'], $extra['type'] => $extra['type_id']];
            $url = ['/site/page', 'mid' => $menu['id']];
        } else {
            $url = ['/site/page', 'mid' => $menu['id']];
        }

        if ($url) {
            return Url::to($url, true);
        } else {
            return null;
        }
    }
}
