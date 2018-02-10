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

    /**
     * Default extra fields
     */
    public function defaultExtraFields()
    {
        $this->extractExtraData();
        return [
            [
                'fieldName' => 'image',
                'inputLabel' => Yii::t('app', 'Image'),
                'inputType' => 'image',
                'valueData' => $this->extraData,
                'defaultValue' => $this->getExtraValue('image'),
            ],
        ];
    }

    /**
     * Extra fields
     */
    public function extraFields()
    {
        return [];
    }

    /**
     * Return extra fields
     */
    public function allExtraFields()
    {
        return ArrayHelper::merge($this->defaultExtraFields(), $this->extraFields());
    }

    /**
     * Render extra tab content
     */
    public function renderExtraTabContent()
    {
        $content = '<p></p>';
        foreach ((array)$this->allExtraFields() as $extra) {
            $content .= \common\widgets\ExtraFieldInput::widget(['options' => $extra]);
        }
        return $content;
    }
}