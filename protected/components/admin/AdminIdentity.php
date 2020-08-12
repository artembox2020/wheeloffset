<?php

/**
 * AdminIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */


class AdminIdentity extends CUserIdentity
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
	public function authenticate()
	{
        
		$criteria = new CDbCriteria();
		$criteria->compare('active', Admin::ACTIVE_USER);
		$criteria->compare('email', $this->username);			
		/* @var $user Admin*/
        $user = Admin::model()->find($criteria);
		
        if (is_null($user)) {
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		}
        else if ($user->password !== md5($this->password))
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        else {
            $this->errorCode=self::ERROR_NONE;
            $this->_id = $user->id;
        }
		
        return !$this->errorCode;
	}

    public function getId() {
        return $this->_id;
    }
}