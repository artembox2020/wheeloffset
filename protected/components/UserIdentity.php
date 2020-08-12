<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */


class UserIdentity extends CUserIdentity
{
    protected $_id;
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
    public function authenticate() {
        /* @var $user User */
        $user = Client::model()->find('LOWER(login)=? and active=?',
                array(strtolower($this->username), Client::ACTIVE_USER));
        if (is_null($user)) {
            $user = Client::model()->find('LOWER(email)=? and active=?',
                    array(strtolower($this->username), Client::ACTIVE_USER));
        }
        if (is_null($user)) {
            $str = implode("",
                    explode("+", implode("", explode("-", $this->username))));
            if ($str[0] == '8') {
                $str[0] = '7';
            }
            if ($str[0] != '7')
                $str = '7' . $str;
            $str = '+' . $str[0] . '-' . substr($str, 1, 3) . '-' . substr($str,
                            4, 3) . '-' . substr($str, 7, 2) . '-' . substr($str,
                            9);

            $user = Client::model()->find('phone=? and active=?',
                    array(strtolower($str), Client::ACTIVE_USER));
        }

        if (is_null($user)) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else if ($user->password !== $this->password)
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        else {
            $this->errorCode = self::ERROR_NONE;
            $this->_id = $user->id;
            $this->setState('role', Role::CLIENT);
        }

        return !$this->errorCode;
    }

    public function getId() {
        return $this->_id;
    }
}