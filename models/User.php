<?php

namespace app\models;

class User extends \yii\base\Object implements \yii\web\IdentityInterface
{
    public $id;
    public $email;
    public $password;
    public $plan_token;

    private static $users = [
        '1' => [
            'id' => '1',
            'email' => 'some@test.ru',
            'password' => 'some',
            'plan_token' => '1',
        ],
        '2' => [
            'id' => '2',
            'email' => 'demo@test.ru',
            'password' => 'demo',
            'plan_token' => '2',
        ],
    ];


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return User|null
     */
    public static function findByEmail($email)
    {
        foreach (self::$users as $user) {
            if (strcasecmp($user['email'], $email) === 0) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by token
     *
     * @param string $email
     * @return User|null
     */
    public static function findByToken($token)
    {
        foreach (self::$users as $user) {
            if (strcasecmp($user['plan_token'], $token) === 0) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }


}
