<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;

class PasswordForm extends Model
{
    public $old_password;
    public $new_password;
    public $confirm_password;

    private $_user;

    public function rules()
    {
        return [
            [['old_password', 'new_password', 'confirm_password'], 'required'],
            ['old_password', 'validatePassword'],
            ['new_password', 'compare', 'compareAttribute' => 'confirm_password', 'skipOnEmpty' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'old_password' => Yii::t('app', 'Old password'),
            'new_password' => Yii::t('app', 'New password'),
            'confirm_password' => Yii::t('app', 'Confirm password'),
        ];
    }

    public function setUser(User $user)
    {
        $this->_user = $user;
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!$this->_user || !$this->_user->validatePassword($this->old_password)) {
                $this->addError($attribute, Yii::t('app', 'Incorrect password'));
            }
        }
    }

    public function changePassword()
    {
        if (!$this->validate()) {
            throw new \yii\base\Exception(implode('<br>', (array)$this->getFirstErrors()));
        }

        try {
            $this->_user->updated_at = time();
            $this->_user->setPassword($this->new_password);

            $this->_user->update(false);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}