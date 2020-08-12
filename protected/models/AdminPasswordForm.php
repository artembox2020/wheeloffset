<?php

/**
 * AdminPasswordForm class.
 * AdminPasswordForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class AdminPasswordForm extends CFormModel
{
	public $password;
	public $confirmPassword;
	
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('confirmPassword, password', 'required'),
			array(
				'confirmPassword', 
				'compare', 
				'compareAttribute'=>'password'
			),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
            'password' => Yii::t('admin', 'Password'),
			'confirmPassword'=>Yii::t('admin', 'Confirm Password'),
		);
	}

}
