<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Security;

/**
 * Class Guardian
 *
 * @since 1.0
 *
 * @package Rf\Core\Security
 */
abstract class Guardian {

	const MASK_NUMBERS = 'n';
	const MASK_LETTERS = 'l';
	const MASK_LETTERS_MAJ = 'L';
	const MASK_SYMBOLS = 's';
	const MASK_SYMBOLS_EXTENDED = 'D';

    /**
     * @var array
     * @since 1.0
     */
    public static $availableHashes = array(
        'md5' => 32,
        'sha1' => 40
    );

    /**
     *
     * @since 1.0
     */
    const HASH_SHA1 = 'sha1';

    /**
     *
     * @since 1.0
     *
     * @param string $string
     * @param string $method (md5|sha1)
     * @return string 
     */
    public static function hash($string, $method) {
        $method = in_array($method, array_keys(self::$availableHashes)) ? $method : 'md5';
        return hash($method, $string);
    }

    /**
     *
     * @since 1.0
     *
     * @return string
     */
    public static function generateTmpPassword() {
        $tmpPwd = '';
        $str = 'abcdefghijklmnpqrstuvwxy0123456789';
        srand((double)microtime()*1000000);
        for($i=0; $i<7; $i++) $tmpPwd .= $str[rand()%strlen($str)];
        return $tmpPwd;
    }

    /**
     *
     * @since 1.0
     *
     * @param $tmpPwd
     * @param null $method
     * @return string
     */
    public static function cryptTmpPassword($tmpPwd, $method = NULL) {
        $method = in_array($method, array_keys(self::$availableHashes)) ? $method : 'md5';
        $tmpCryptPwd = strrev(hash($method, $tmpPwd));
        return substr($tmpCryptPwd, self::$availableHashes[$method]/2, self::$availableHashes[$method]/2).substr($tmpCryptPwd, 0, self::$availableHashes[$method]/2);
    }

    /**
     *
     * @since 1.0
     *
     * @param $tmpPwd
     * @param null $method
     * @return string
     */
    public static function decryptTmpPassword($tmpPwd, $method = NULL) {
        $method = in_array($method, array_keys(self::$availableHashes)) ? $method : 'md5';
        $tmpDecryptPwd = substr($tmpPwd, self::$availableHashes[$method]/2, self::$availableHashes[$method]/2).substr($tmpPwd, 0, self::$availableHashes[$method]/2);
        return strrev($tmpDecryptPwd);
    }

    /**
     *
     * @since 1.0
     *
     * @param int $charNb
     * @return string
     */
    public static function generateValidationCode($charNb = 10) {
        $validationCode = '';
        $chaine = 'abcdefghijklmnpqrstuvwxy0123456789';
        srand((double)microtime()*1000000);
        for($i=0; $i<$charNb; $i++) $validationCode .= $chaine[rand()%strlen($chaine)];
        return $validationCode;
    }
    
    /**
     * This function generate a random key from a custom character list
     *
     * @since 1.0
     *
     * @param int $length
     * @param string $mask 
     *      -n: numbers 0-9,
     *      -l: lettersMin a-z,
     *      -L: letterMaj A-Z,
     *      -s: symbols1 !-_+,.;:
     *      -S: symbols2 ()[]{}*%?€'"\|/#@<>
     * @return string
     */
    public static function generateSecurityKey($length = 16, $mask = '-n-l-L-s-S') {
        
        $numbers = '0123456789';
        $lettersMin = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lettersMaj = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $symbols = '!-_+,.;:';
        $symbols2 = '()[]{}*%?€\'"\\|/#@<>';
        
        $charPool = str_replace(
            array('-n', '-l', '-L', '-s', '-S'), 
            array($numbers, $lettersMin, $lettersMaj, $symbols, $symbols2), 
            $mask
        );
        
        $key = substr(str_shuffle($charPool), 0, $length);
        return $key;

    }

	/**
	 * This function generate a random key from a custom character list
	 *
	 * @param int $length
	 * @param array $masks
	 *
	 * @return string
	 */
    public static function generateSecurityKeyNew(int $length = 16, array $masks): string {

    	$masksArray = [
    		self::MASK_NUMBERS => '0123456789',
    		self::MASK_LETTERS => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
    		self::MASK_LETTERS_MAJ => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
    		self::MASK_SYMBOLS => '!-_+,.;:',
    		self::MASK_SYMBOLS_EXTENDED => '()[]{}*%?€\'"\\|/#@<>',
	    ];
    	$charPool = '';
    	$key = '';

	    foreach ($masks as $mask) {

	    	if(isset($masksArray[$mask])) {
	    		$charPool .= $masksArray[$mask];
		    }

	    }

	    $charPoolLength = strlen($charPool);
	    for($i = 0; $i < $length ; $i++) {

	    	// Use the $charPool string as array
	    	$key .= $charPool[rand(0, $charPoolLength - 1)];

	    }

        return $key;

    }

}