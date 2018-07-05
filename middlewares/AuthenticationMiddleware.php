<?php
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . 'autoloader.php';

class AuthenticationMiddleware
{
    public function __invoke($request, $response, $next) {
        $headers = getallheaders();
		$headers = array_change_key_case($headers, CASE_LOWER);
        if (empty($headers['fineform-access-token'])) {
            $response = $response->withStatus(401);
            $response = $response->withHeader('Content-Type', 'application/json');
			
            $output = array();
            $output["response_status"]	= false;
			$output["response_message"]	= "Failed to authenticate, access token not found.";
			
            $response->write(json_encode($output));
            return $response;
        } else {
            try {
                $access_token = $headers['fineform-access-token'];
                $data = JWT::decode($access_token, KEY, ALGO);
                $user = UserController::shared()->getUserByEmailId($data->username);
				if (is_null($user)) {
					throw new Exception("No user found with given data.");
				} else {
					if($user['role'] == ROLE_ADMINISTRATOR) {
						$request = $request->withAttribute('user_id', $user['id']);
                        $request = $request->withAttribute('email_id', $user['email_id']);
                        $request = $request->withAttribute('user_role', $user['role']);
						
                        $response = $next($request, $response);
                        return $response;
					} else {
						throw new Exception("Not an authorized user to access data.");
					}
				}
            } catch(Exception $ex) {
                $response = $response->withStatus(401);
                $response = $response->withHeader('Content-Type', 'application/json');
				
                $output = array();
                $output["response_status"]	= false;
    			$output["response_message"]	= "Failed to authenticate, access token malformed.";
                $output["response_data"] = [];
				
                $response->write(json_encode($output));
                return $response;
            }
        }
    }
}
?>