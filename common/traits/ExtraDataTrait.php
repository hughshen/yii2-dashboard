<?php

namespace common\traits;

use Yii;
use yii\helpers\ArrayHelper;

trait ExtraDataTrait
{
    public $extractDone = false;
    public $extraData = [];

    protected function checkExtractDone()
    {
        if (!$this->extractDone) {
            $this->extractExtraData();
        }
    }

    public function setExtraField($field, $value = null)
    {
        $this->checkExtractDone();
        $this->extraData[$field] = $value;
    }

    public function setExtraFieldArray($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $val) {
                $this->setExtraField($key, $val);
            }
        }
    }

    public function getExtraValue($field)
    {
        $this->checkExtractDone();
        if (isset($this->extraData[$field])) {
            return $this->extraData[$field];
        } else {
            return null;
        }
    }

    public function saveExtraData($doSave = false)
    {
        try {
            $this->extra_data = json_encode($this->extraData);
            if ($doSave) {
                $this->save();
            }
        } catch (\Exception $e) {

        }
    }

    public function extractExtraData()
    {
        $this->extractDone = true;
        try {
            $this->extraData = json_decode($this->extra_data, true);
        } catch (\Exception $e) {

        }
    }

    public static function combineExtraData($data)
    {
        if (!is_array($data) || !$data) return $data;

        // Single
        $isSingle = isset($data['id']);
        $data = $isSingle ? [$data] : $data;

        $newData = [];
        foreach ($data as $key => $val) {
            $extra = @json_decode($val['extra_data'], true);
            unset($val['extra_data']);
            if (is_array($extra)) {
                foreach ($extra as $k => $v) {
                    $k = 'extra_' . $k;
                    $val[$k] = $v;
                }
            }
            $newData[] = $val;
        }

        return $isSingle ? $newData[0] : $newData;
    }

    /**
     * Default extra fields
     */
    public function defaultExtraFields()
    {
        $this->extractExtraData();

        $defaultExtraFields = [
            // [
            //     'fieldName' => 'image',
            //     'inputLabel' => Yii::t('app', 'Image'),
            //     'inputType' => 'image',
            //     'valueData' => $this->extraData,
            //     'defaultValue' => $this->getExtraValue('image'),
            // ],
        ];

        return $defaultExtraFields;
    }

    /**
     * Extra fields
     */
    public function appendExtraFields()
    {
        return [];
    }

    /**
     * Return extra fields
     */
    public function allExtraFields()
    {
        return ArrayHelper::merge($this->defaultExtraFields(), $this->appendExtraFields());
    }

    /**
     * Render extra tab content
     */
    public function renderExtraTabContent()
    {
        if (!isset(Yii::$app->extraFieldInput)) {
            throw new \yii\base\InvalidConfigException();
        }

        $inputClass = '\\' . get_class(Yii::$app->extraFieldInput);

        $content = '';
        foreach ((array)$this->allExtraFields() as $extra) {
            $content .= $inputClass::widget(['options' => $extra]);
        }
        return $content;
    }
}
