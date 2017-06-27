<?php
namespace app\classes;

use app\models\User;

class ApiHelper {

    /**
     * Возвращает токен пользователя по заданному email.
     *
     * @param $email
     * @return string|null token в случае успеха.<br/>
     * null - если пользователя с таким email не существует, ЛИБО для него не задано поле plan_token
     */
    public static function getTokenByEmail($email) {
        $user = User::findByToken($email);
        if ($user === null ) {
            return null;
        }
        return $user->plan_token;
    }

    public static function getUserByToken($token) {
        if( empty($token) ) {
            return null;
        }
        $user = User::findByToken($token);
        return $user;
    }
}