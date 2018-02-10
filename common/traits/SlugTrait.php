<?php

namespace common\traits;

use Yii;

trait SlugTrait
{
    /**
     * Get slug from string
     */
    public function getSlug($string)
    {
        $string = preg_replace(
            ['/\s/', '/_/', '/-+/', '/[^\w\s-\x7f-\xff]/'],
            ['-', '-', '-', ''],
            $string
        );

        return strtolower($string);
    }

    /**
     * Set slug
     */
    public function setSlug($default = '')
    {
        try {
            if (empty($this->slug)) {
                $this->slug = $default;
            }
            $this->slug = $this->getSlug($this->slug);
            $this->slug = $this->uniqueSlug($this->slug);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Check slug if unique
     */
    public function uniqueSlug($slug)
    {
        $className = self::className();
        $className = '\\' . $className;

        $maxLoop = 10;
        $loopIndex = 0;
        while (!empty($slug) && ($model = $className::findOne(['slug' => $slug])) !== null && ($this->id === null || ($this->id !== null && $this->id != $model->id)) && $loopIndex < $maxLoop) {
            $arr = explode('-', $slug);
            $last = end($arr);
            if (is_numeric($last) && isset($arr[1])) {
                $last = (int)$last + 1;
                array_pop($arr);
                array_push($arr, $last);
                $slug = implode('-', $arr);
            } else {
                $slug .= '-1';
            }
            $loopIndex++;
        }

        if ($loopIndex == $maxLoop || empty($slug)) {
            $slug = uniqid() . '-' . time();
        }

        return $slug;
    }
}
