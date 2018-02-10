<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    use \common\traits\ExtraDataTrait;
    use \common\traits\CrudModelTrait;

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 20;
    const ROLE_MANAGER = 'manager';
    const ROLE_USER = 'user';
    const GROUP_BACKEND = 'backend';
    const GROUP_FRONTEND = 'frontend';

    // Add
    public $password;
    public $password2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%site_user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Validate
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique'],

            ['role', 'default' , 'value' => self::ROLE_USER],
            ['role_group', 'default', 'value' => self::GROUP_FRONTEND],
            [['role', 'role_group'], 'string', 'max' => 32],

            ['status', 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE, self::STATUS_DELETED]],

            [['created_at', 'updated_at', 'deleted_at'], 'integer'],
            [['created_at', 'updated_at'], 'default', 'value' => time()],

            // Add
            [['password', 'password2'], 'required', 'on' => ['backend_create']],
            [['password', 'password2'], 'string', 'min' => 4],
            ['password2', 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => Yii::t('app', 'Username'),
            'email' => Yii::t('app', 'Email'),
            'role' => Yii::t('app', 'Role'),
            'role_group' => Yii::t('app', 'Role Group'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted_at' => Yii::t('app', 'Deleted At'),

            // Add
            'password' => Yii::t('app', 'Password'),
            'password2' => Yii::t('app', 'Confirm Password'),
        ];
    }

    public function saveModel()
    {
        if ($this->isNewRecord) {
            $this->generateAuthKey();
        } else {
            $this->updated_at = time();
        }
        
        if ($this->password) {
            $this->setPassword($this->password);
        }

        if ($this->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Successfully'));
            return true;
        }

        Yii::$app->session->setFlash('error', Yii::t('app', 'Failed'));
        return false;
    }

    public function deleteModel()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $randomString = Yii::$app->security->generateRandomString();
            $this->username .= "_{$randomString}";
            $this->email .= "_{$randomString}";

            $this->status = self::STATUS_DELETED;
            $this->deleted_at = time();

            $this->save(false);

            $transaction->commit();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Deleted successfully.'));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne([
            'username' => $username,
            'status' => self::STATUS_ACTIVE,
            'role' => self::ROLE_USER,
            'role_group' => GROUP_FRONTEND,
        ]);
    }

    /**
     * Finds manager by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findManagerByUsername($username)
    {
        return static::findOne([
            'username' => $username,
            'status' => self::STATUS_ACTIVE,
            'role' => self::ROLE_MANAGER,
            'role_group' => self::GROUP_BACKEND,
        ]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
