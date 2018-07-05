<?php
require_once PROJECT_ROOT . DIRECTORY_SEPARATOR . 'autoloader.php';

class UserHelper
{
    private static $instance;
    public static function shared()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public function authenticate($roles, $username, $password)
    {
        $user = DbHelper::shared()->select('fnf_users', array('[><]fnf_roles' => array('user_id' => 'user_id')), array('fnf_users.user_id(id)', 'fnf_users.first_name(first_name)', 'fnf_users.last_name(last_name)', 'fnf_users.email_id(username)', 'fnf_roles.role_title(role)'), array('AND' => array('fnf_users.email_id' => $username, 'fnf_users.password' => $password, 'fnf_roles.role_title' => $roles)));
        if ((sizeof($user) > 0) && (sizeof($user[0]) > 0)) {
            return $user[0];
        } else {
            return NULL;
        }
    }

    public function getUserByEmailId($email_id, $role = NULL)
    {
		$users = array();
		if (is_null($role)) {
			$users = DbHelper::shared()->select('fnf_users', array('[><]fnf_roles' => array('user_id' => 'user_id')), array('fnf_users.user_id(id)', 'fnf_users.first_name(first_name)', 'fnf_users.last_name(last_name)', 'fnf_users.email_id(email_id)', 'fnf_roles.role_title(role)', 'fnf_users.password(password)'), array('AND' => array('fnf_users.email_id' => $email_id)));
		} else {
			$users = DbHelper::shared()->select('fnf_users', array('[><]fnf_roles' => array('user_id' => 'user_id')), array('fnf_users.user_id(id)', 'fnf_users.first_name(first_name)', 'fnf_users.last_name(last_name)', 'fnf_users.email_id(email_id)', 'fnf_roles.role_title(role)', 'fnf_users.password(password)'), array('AND' => array('fnf_users.email_id' => $email_id, 'fnf_roles.role_title' => $role)));
		}
		
        if ((sizeof($users) > 0) && (sizeof($users[0]) > 0)) {
            return $users[0];
        }

        return NULL;
    }

    public function changePassword($id, $currentPassword, $newPassword)
    {
        $users = DbHelper::shared()->select('fnf_users', array('user_id'), array('AND' => array('user_id' => $id, 'password' => $currentPassword)));
        if ((sizeof($users) > 0) && (sizeof($users[0]) > 0)) {
            DbHelper::shared()->update('fnf_users', array('password' => $newPassword), array("AND" => array('user_id' => $id)));
            return $users[0];
        } else {
            return NULL;
        }
    }
}
?>
