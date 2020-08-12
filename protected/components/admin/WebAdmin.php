<?php

class WebAdmin extends CWebUser {
    private $_model = null;

    function getRole() {
        if ($admin = $this->getModel()) {
            return $admin->role_id;
        }
    }

    public function getModel() {
        if (!$this->isGuest && $this->_model === null) {
            $this->_model = Admin::model()->findByPk($this->id);
        }

        return $this->_model;
    }
}