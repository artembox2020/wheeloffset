<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sergey
 * Date: 19.05.13
 * Time: 2:00
 */

class WebUser extends CWebUser {
    private $_model = null;

    function getRole() {
        if ($user = $this->getModel()) {
            return $user->role_id;
        }
    }

    public function getUser() {
        if (!$this->isGuest) {
            return User::model()->findByPk(Yii::app()->user->id);
        }
        return null;
    }

    public function getModel() {
        if (!$this->isGuest && $this->_model === null) {
            $this->_model = User::model()->findByPk($this->id);
        }

        return $this->_model;
    }
}