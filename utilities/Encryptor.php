<?php
class Encryptor
{
    /**
     * @var Singleton The reference to *Singleton* instance of this class
     */
    private static $instance;

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function shared()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }

    public function encrypt($raw_string)
    {
		$encrypt_method = "AES-256-CBC";
		$secret_iv = "25c6c7ff35b9979b151f2136cd13b0ff";
		$secret_key = "da39a3ee5e6b4b0d3255bfef95601890afd80709";
		
        $key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		$encrypted_string = openssl_encrypt($raw_string, $encrypt_method, $key, 0, $iv);
        $encrypted_string = base64_encode($encrypted_string);
		
		return $encrypted_string;
    }
	
	public function decrypt($encrypted_string)
    {
		$encrypt_method = "AES-256-CBC";
		$secret_iv = "25c6c7ff35b9979b151f2136cd13b0ff";
		$secret_key = "da39a3ee5e6b4b0d3255bfef95601890afd80709";
		
        $key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		$decrypted_string = openssl_decrypt(base64_decode($encrypted_string), $encrypt_method, $key, 0, $iv);
		
		return $decrypted_string;
    }
}
?>
