<?php
require_once PROJECT_ROOT . DIRECTORY_SEPARATOR . 'autoloader.php';

class UserInterface
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

    public function authenticate()
    {
        return function ($request, $response, $args) {
            $body = $request->getBody();
            $input = json_decode($body);

            $errors = array();
            if (empty($input->username)) {
                array_push($errors, array('param' => 'username', 'msg' => 'Please enter username'));
            } elseif (!filter_var($input->username, FILTER_VALIDATE_EMAIL)) {
                array_push($errors, array('param' => 'username', 'msg' => 'Please enter valid username'));
            }

            if (empty($input->password)) {
                array_push($errors, array('param' => 'password', 'msg' => 'Please enter password'));
            }

            $output = array();
            $response = $response->withHeader('Content-Type', 'application/json');
            if (sizeof($errors) > 0) {
                $output["response_status"] = false;
                $output["response_message"] = "Please enter your username and password.";
                $output["response_data"] = ["error_info" => $errors];

                $response = $response->withStatus(400);
                $response->write(json_encode($output));
            } else {
                $user = UserController::shared()->authenticate(array(ROLE_ADMINISTRATOR), $input->username, $input->password);
                if (is_null($user)) {
                    $output["response_status"] = false;
                    $output["response_message"] = "Failed to authenticate, please check your username and password.";
                    $output["response_data"] = [];
					
                    $response = $response->withStatus(400);
                } else {
					$authentication_info = array("access_token" => JWT::encode($user, KEY, ALGO));
                    if (is_null($authentication_info)) {
                        $output["response_status"] = false;
                        $output["response_message"] = "Failed to authenticate, please check your username and password.";
                        $output["response_data"] = [];
                        
                        $response = $response->withStatus(400);
                    } else {
                        $output["response_status"] = true;
                        $output["response_message"] = "";
                        $output["response_data"] = $authentication_info;
                        
                        $response = $response->withStatus(200);
                    }
				}

                $response->write(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
            }

            return $response;
        };
    }

    public function changePassword()
    {
        return function ($request, $response, $args) {
            $body = $request->getBody();
            $input = json_decode($body);

            $errors = array();
            if (empty($input->currentPassword)) {
                array_push($errors, array('param' => 'username', 'msg' => 'Please enter current password'));
            }

            if (empty($input->newPassword)) {
                array_push($errors, array('param' => 'password', 'msg' => 'Please enter new password'));
            }

            $output = array();
            $response = $response->withHeader('Content-Type', 'application/json');
            if (sizeof($errors) > 0) {
                $output["response_status"] = false;
                $output["response_message"] = "Please enter your current and new password.";
                $output["response_data"] = ["error_info" => $errors];

                $response = $response->withStatus(400);
                $response->write(json_encode($output));
            } else {
                $user_id = $request->getAttribute('user_id');
                $change_password_info = UserController::shared()->changePassword($user_id, $input->currentPassword, $input->newPassword);
                if (is_null($change_password_info)) {
                    $output["response_status"] = false;
                    $output["response_message"] = "Failed to change password. Please check your current password.";

                    $response = $response->withStatus(400);
                } else {
                    $output["response_status"] = true;
                    $output["response_message"] = "Password changed successfully.";

                    $response = $response->withStatus(200);
                }

                $response->write(json_encode($output));
            }

            return $response;
        };
    }
}
?>
