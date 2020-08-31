<?php

namespace frontend\models;

use Yii;
use yii\helpers\Url;

class Menu extends \common\models\cms\Menu
{
    public function getParentMenu()
    {
        return $this->hasOne(static::class, ['id' => 'parent'])->with(['translated'])->andOnCondition(['status' => 1, 'type' => static::typeName(), 'deleted_at' => 0]);
    }

    public function getFirstMenu()
    {
        return $this->hasOne(static::class, ['parent' => 'id'])->with(['translated'])->andOnCondition(['status' => 1, 'type' => static::typeName(), 'deleted_at' => 0])->orderBy('sorting ASC, created_at DESC');
    }

    public static function commonQuery($condition = [])
    {
        $query = self::find()
            ->with(['translated', 'parentMenu', 'firstMenu'])
            ->where(['status' => 1, 'type' => static::typeName(), 'deleted_at' => 0])
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
        if (!$data) {
            return $data;
        }

        $selfBuild = ['parentMenu', 'firstMenu'];
        foreach ($selfBuild as $val) {
            if (isset($data[$val])) {
                $data[$val] = self::buildData($data[$val]);
            }
        }

        $data['menu_url'] = self::buildUrl($data);

        // Translate
        $data = self::combineTranslatedData($data);

        // Extra data
        if (isset($data['extra_data'])) {
            $extra = @json_decode($data['extra_data'], true);
            if (is_array($extra) && $extra) {
                foreach ($extra as $key => $val) {
                    $data[$key] = $val;
                }
            }
            unset($data['extra_data']);
        }

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

        $extra = @json_decode($menu['extra_data'], true);

        $url = '';
        if (isset($extra['route']) && !empty($extra['route'])) {
            $url = '/' . ltrim($extra['route'], '/');
            $url = [$url, 'mid' => $menu['id']];
        } elseif (isset($extra['link']) && !empty($extra['link'])) {
            $url = $extra['link'];
            if (stripos(trim($extra['link']), 'http') !== 0) {
                if (strpos($url, '?') !== false) {
                    $url .= '&mid=' . $menu['id'];
                } else {
                    $url .= '?mid=' . $menu['id'];
                }
            }
        } elseif (
            isset($extra['type']) &&
            isset($extra['type_id']) &&
            !empty($extra['type']) &&
            !empty($extra['type_id'])
        ) {
            $url = ['/site/page', 'mid' => $menu['id']];
        }

        if ($url) {
            $url = Url::to($url, true);
        }

        return $url;
    }
}