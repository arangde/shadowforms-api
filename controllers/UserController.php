<?php
require_once PROJECT_ROOT . DIRECTORY_SEPARATOR . 'autoloader.php';

class UserController
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
        $user = UserHelper::shared()->authenticate($roles, $username, md5($password));
        if (is_null($user)) {
            return NULL;
        }
		
		return $user;
    }
	
	public function getUserByEmailId($email_id, $role = NULL)
    {
        return UserHelper::shared()->getUserByEmailId($email_id, $role);
    }

    public function changePassword($id, $currentPassword, $newPassword)
    {
        $info = UserHelper::shared()->changePassword($id, md5($currentPassword), md5($newPassword));
        return $info;
    }
}
?>
