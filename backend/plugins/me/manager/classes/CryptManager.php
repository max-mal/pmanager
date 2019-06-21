<?php namespace Me\Manager\Classes;

use Cms\Classes\ComponentBase;
use RainLab\User\Facades\Auth;
use Me\Manager\Models\Key;

class CryptManager {


	public static function generateRSAKeypair(){
	
		$rsaConfig = [
	        "digest_alg" => "sha512",
	        "private_key_bits" => 4096,
	        "private_key_type" => OPENSSL_KEYTYPE_RSA,
	    ];

        // Create the private and public key
        $res = openssl_pkey_new($rsaConfig);

        // Extract the private key from $res to $privKey
        openssl_pkey_export($res, $privKey);

        $pubKey = openssl_pkey_get_details($res);
		$pubKey = $pubKey["key"];

        return [
        	'private_key' => $privKey,
        	'public_key' => $pubKey,
        ];
	}

	public static function aesEncrypt($plaintext, $key) {
		
		$cipher = "aes-128-gcm";

		$keyHash = md5($key);

		$plaintext = md5($plaintext) . $plaintext;

	    $ivlen = openssl_cipher_iv_length($cipher);
	    $iv = openssl_random_pseudo_bytes($ivlen);
	    $ciphertext = openssl_encrypt($plaintext, $cipher, $keyHash, $options=0, $iv, $tag);

	    return [
	    	'ciphertext' => base64_encode($ciphertext),
	    	'iv' => $iv,
	    	'tag' => $tag,
	    ];
	    
	}

	public static function aesDecrypt($ciphertext, $key, $iv, $tag){

		$cipher = "aes-128-gcm";

		$ciphertext = base64_decode($ciphertext);

		$keyHash = md5($key);

		$plaintext = openssl_decrypt($ciphertext, $cipher, $keyHash, $options=0, $iv, $tag);

		$plaintext_hash = substr($plaintext, 0, 32);
		$plaintext_text = substr($plaintext, 32);

		if (md5($plaintext_text) == $plaintext_hash) return $plaintext_text;
	    
	    return false;

	}

}