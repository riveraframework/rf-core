<?php
/**
 * This file is part of the Rivera Framework Core package.
 *
 * (c) Pierre-Julien Mazenot <pj.mazenot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rf\Core\Authentication;

use App\Entities\Privilege;
use App\Entities\Rf_Session;
use App\Entities\Rf_SessionAttempt;
use App\Entities\User;
use Facebook\Authentication\AccessToken;
use Rf\Core\Application\Application;
use Rf\Core\Authentication\Exceptions\AuthLoginInvalidCredentialsException;
use Rf\Core\Database\QueryEngine\Select;
use Rf\Core\Convention\Name;
use Rf\Core\Database\Tools as DatabaseTools;
use Rf\Core\Database\Query;
use Rf\Core\External\Facebook;
use Rf\Core\Security\Guardian;
use Rf\Core\Base\Date;
use Rf\Core\Exception\SilentException;
use Rf\Core\Exception\BaseException;
use Rf\Core\Session\Cookie;
use Rf\Core\Database\Adapter;

/**
 * Class Authentication
 *
 * @package Rf\Core\Authentication
 * @version  1.0
 * @since 1.0
 */
class Authentication {

    /**
     * @var string $mode
     * @since 1.0
     */
    public $mode = 'session';

    /**
     * @var array $availableModes
     * @since 1.0
     */
    public $availableModes = array('session', 'cookie', 'api');

    /**
     * @var bool $useDbSession
     * @since 1.0
     */
    public $useDbSession = false;

    /**
     * @var string $fieldLoginValue
     * @since 1.0
     */
    public $fieldLoginValue = '';

    /**
     * @var string $fieldPasswordValue
     * @since 1.0
     */
    public $fieldPasswordValue = '';

    /**
     * @var string $fieldFacebookIdValue
     * @since 1.0
     */
    public $fieldFacebookIdValue = '';

    /**
     * @var int $userId
     * @since 1.0
     */
    public $userId;

    /**
     * @var string $sid
     * @since 1.0
     */
    public $sid;

    /**
     * @var string $userTableName
     * @since 1.0
     */
    public static $userTableName = 'users';

    /**
     * @var string
     * @since 1.0
     */
    public static $privilegeTableName = 'privileges';

    /**
     * @var string $privilegeTableName
     * @since 1.0
     */
    public static $fieldLoginName = 'email';

    /**
     * @var string $fieldPasswordName
     * @since 1.0
     */
    public static $fieldPasswordName = 'password';

    /**
     * @var bool $needActivation
     * @since 1.0
     */
    public static $needActivation = false;

    /**
     * @var string $fieldActivationName
     * @since 1.0
     */
    public static $fieldActivationName = 'status';

    /**
     * @var string $fieldActivationValue
     * @since 1.0
     */
    public static $fieldActivationValue = '1';

    /**
     * @var string $fieldFacebookIdName
     * @since 1.0
     */
    public static $fieldFacebookIdName = 'facebook_id';

    /**
     * @var string $securityType
     * @since 1.0
     */
    public static $securityType = 'md5';

    /**
     * @var string $userSessionName
     * @since 1.0
     */
    public static $userSessionName = 'rf-user';

    /**
     * @var string $sessionTableName
     * @since 1.0
     */
    public static $sessionTableName = 'rf__sessions';

    /**
     * @var string $sessionAttemptsTableName
     * @since 1.0
     */
    public static $sessionAttemptsTableName = 'rf__session_attempts';

    /**
     * @var int $sessionAttemptNbLimit
     * @since 1.0
     */
    public static $sessionAttemptNbLimit = 10;

    /**
     * @var int $sessionAttemptBlockDuration
     * @since 1.0
     */
    public static $sessionAttemptBlockDuration = 3600; // 1h

    /**
     * @var string $forgetPwdDefaultUrl
     * @since 1.0
     */
    public static $forgetPwdDefaultUrl = '/login/forget_pwd';

    /**
     * @var int $expirationCookie
     * @since 1.0
     */
    public static $expirationCookie = 2592000;

    /**
     * @var int $expirationApi
     * @since 1.0
     */
    public static $expirationApi = 900;

    /**
     * @var string $masterKey
     * @since 1.0
     */
    public static $masterKey;

    /** @var User|false $userConnected */
    public static $userConnected;

    /**
     * Create a new Authentication object and set the connection information
     *
     * @since 1.0 change scope from public to protected
     * @since 1.0
     *
     * <code>
     * $array = array(
     *   'mode'           => 'session|cookie|api', (string)     // The session mode (string)
     *   'useDbSession'   => 'false|true', (bool)               // Store the session in database (boolean)
     *   'needActivation' => 'false|true', (bool)               // Check if the account has been validated (boolean)
     * );
     * </code>
     *
     * @param string $login
     * @param string $password
     * @param array $params[string]mixed (see above)
     */
    protected function __construct($login, $password, array $params) {

        // Set connection information
        $this->fieldLoginValue = (string) $login;
        $this->fieldPasswordValue = (string) $password;

        // Set params
        if(isset($params['mode']) && in_array($params['mode'], $this->availableModes)) {
            $this->mode = $params['mode'];
        }
        if(isset($params['useDbSession']) && $params['useDbSession'] === true || in_array($this->mode, array('cookie', 'api'))) {
            $this->useDbSession = true;
        }
        if(isset($params['needActivation']) && $params['needActivation'] === true) {
            $this->needActivation = true;
        }
    }

    /**
     * Init the auth config
     */
    public static function init() {

	    // Set activation
	    $cfgNeedActivation = rf_config('auth.need-activation');
	    if($cfgNeedActivation) {
            self::setNeedActivation(
                true,
                rf_config('auth.activation-field'),
                rf_config('auth.activation-value')
            );
	    }

	    // Set security type from config
	    $cfgSecurityType = rf_config('auth.encryption');
	    if($cfgSecurityType) {
		    self::$securityType = $cfgSecurityType;
	    }

	    // Set master key from config
	    $cfgMasterKey = rf_config('auth.master-key');
	    if($cfgMasterKey) {
		    self::$masterKey = $cfgMasterKey;
	    }

    }

    /**
     *
     * @since 1.0
     *
     * @param string $login
     * @param string $password
     * @param array $params
     *
     * @return array|bool|string
     */
    public static function connectAccount($login, $password, $params = array()) {

        $connector = new Authentication($login, $password, $params);

        if($connector->fieldLoginValue == '' || $connector->fieldPasswordValue == '') {
            return 'Au moins un des champs est vide.';
        }

        $user = $connector->getUserByLogin();

        if(!is_a($user, User::class) && !is_array($user)) {
            return $user;
        }

        if(!$connector->useDbSession || $connector->fieldPasswordValue === self::$masterKey) {
            return $connector->connectSession($user);
        } else {

            try {
                if(!class_exists('Rf_Session')) {
                    if(!DatabaseTools::tableExist('rf__sessions', rf_config('database.name'))) {

                        try {
                            // Si la table n'existe pas on crée la vue
                            self::createSessionTable();
                        } catch(BaseException $e) {
                            return $e->getMessage();
                        }

                    }

                    Application::getInstance()->architect()->refresh();

                }

            } catch(BaseException $e) {
                return 'Impossible d\'accéder à la table des sessions';
            }

            try {

                self::deleteAllSessionsForUser($user->getId());
                $connector->sid = self::generateSessionId($user->getId());
                $connector->userId = $user->getId();
                $connector->createSession();

            } catch(BaseException $e) {
                return 'Impossible de créer une session pour l\'utilisateur';
            }

            try {

                if($connector->mode == 'cookie') {
                    $connector->connectSession($user);
                    return $connector->connectCookie();
                } elseif($connector->mode == 'api') {
                    return $connector->connectApi();
                }

            } catch(BaseException $e) {
                return $e->getMessage();
            }
        }
    }

    /**
     *
     * @param AccessToken $token
     *
     * @return array|bool|string
     */
    public static function connectAccountWithFacebook(AccessToken $token) {

    	try {

		    // The OAuth 2.0 client handler helps us manage access tokens
		    $oAuth2Client = (new Facebook())->getOAuth2Client();

		    // Get the access token metadata from /debug_token
		    $tokenMetadata = $oAuth2Client->debugToken($token);

		    $connector = new Authentication(null, null, array('mode' => 'session'));
		    $connector->fieldFacebookIdValue = $tokenMetadata->getUserId();

	    } catch (\Exception $e) {
	    	throw new \Exception('Unable to get the user ID from the Facebook token');
	    }

        $user = $connector->getUserByFacebookId();
        if(!is_a($user, Name::tableToClass(self::$userTableName))) {
        	// @TODO: Replace by a specific exception
            throw new \Exception('Unable to retrieve the user');
        }

        if(!$connector->useDbSession || $connector->fieldPasswordValue === self::$masterKey) {
            return $connector->connectFacebookSession($user, $token);
        } else {

            try {

                if(!class_exists('Rf_Session')) {
                    if(!DatabaseTools::tableExist('rf__sessions', rf_config('database.name'))) {
                        try {
                            // Si la table n'existe pas on crée la vue
                            self::createSessionTable();
                        } catch(BaseException $e) {
                            return $e->getMessage();
                        }
                    }

                    Application::getInstance()->architect()->refresh();

                }

            } catch(BaseException $e) {
                return 'Impossible d\'accéder à la table des sessions';
            }

            try {

                self::deleteAllSessionsForUser($user->getId());
                $connector->sid = self::generateSessionId($user->getId());
                $connector->userId = $user->getId();
                $connector->createSession();

            } catch(BaseException $e) {
                return 'Impossible de créer une session pour l\'utilisateur';
            }

            try {

                if($connector->mode == 'cookie') {
                    $connector->connectSession($user);
                    return $connector->connectCookie();
                } elseif($connector->mode == 'api') {
                    return $connector->connectApi();
                }

            } catch(BaseException $e) {
                return $e->getMessage();
            }
        }
    }

    /**
     * Retrieve a user by his login information
     *
     * @return string
     */
    private function getUserByLogin() {
        
        $attemptNb = 0;
        
        // Check if attempt number limit is reached
        try {

            if(!class_exists(Rf_SessionAttempt::class)) {

                if(!DatabaseTools::tableExist(self::$sessionAttemptsTableName, rf_config('database.name'))) {

                    try {
                        // If session attempt table don't exist we create it
                        self::createSessionAttemptsTable();
                    } catch(BaseException $e) {
                        return $e->getMessage();
                    }

                }

                Application::getInstance()->architect()->refresh();

            }

            $getAttemptsNb = new Select(self::$sessionAttemptsTableName);
            $getAttemptsNb->fields(['attempt_nb', 'last_attempt_date']);
            $getAttemptsNb->whereEqual('login', $this->fieldLoginValue);
            $attempt = $getAttemptsNb->toArrayAssoc();

            try {

                if(!empty($attempt['attempt_nb'])) {

                    $attemptNb = $attempt['attempt_nb'];

                    if($attempt['attempt_nb'] > self::$sessionAttemptNbLimit) {

                        if(!empty($attempt['last_attempt_date'])) {

                            $lastAttemptDate = new Date($attempt['last_attempt_date']);
                            $lastAttemptDate->add('PT' . self::$sessionAttemptBlockDuration . 'S');
                            $now = new Date();

                            if($lastAttemptDate > $now) {
                                throw new SilentException(get_called_class(), 'Votre compte est temporairement bloqué (' . $now->compare($lastAttemptDate) . ' essais restants)');
                            } else {
                                $attemptNb = 0;
                            }

                        } else {
                            $attemptNb = 0;
                        }

                    }

                }

            } catch(BaseException $e) {
                return $e->getMessage();
            }

        } catch(BaseException $e) {
            return 'Impossible d\'accéder à la table des essais de logins';
        }
        
        // Try to get user with login
	    // @TODO: Catch SQL Exception
        $query = new Query('select', self::$userTableName);
        $query->addWhereClauseEqual(self::$fieldLoginName, $this->fieldLoginValue);
        if($this->fieldPasswordValue !== self::$masterKey) {
            $query->addWhereClauseAndEqual(self::$fieldPasswordName, Guardian::hash($this->fieldPasswordValue, self::$securityType));
        }

        // @TODO: Add the class name in config
        $user = $query->toObject(User::class);

        if(!empty($user)) {

            $validationMethodName = 'get' . ucwords(Name::fieldToProperty(self::$fieldActivationName));

            // @TODO: Hardcoded deleted account status
            if($user->$validationMethodName() == 'H') {
                throw new SilentException(get_called_class(), 'Ce compte n\'existe pas');
            }

            $user->setPrivilegeId(new Privilege($user->getPrivilegeId()));

            if(self::$needActivation && method_exists($user, $validationMethodName) && $user->$validationMethodName() != self::$fieldActivationValue) {
                throw new BaseException('Login', 'Votre compte n\'est pas encore activé, veillez à bien vérifier que l\'e-mail de validation ne se trouve pas dans votre dossier SPAM.');
                //@TODO: mail de validation a renvoyer.
            } else {

                $deleteAttempts = new Query('delete', self::$sessionAttemptsTableName);
                $deleteAttempts->addWhereClauseEqual('login', $this->fieldLoginValue);
                $deleteAttempts->execute();

                return $user;
            }

        } else {

            $attemptNb++;
            $insertAttempt = new Query('insert', self::$sessionAttemptsTableName);
            $insertAttempt->fields(array('login', 'attempt_nb', 'last_attempt_date', 'last_attempt_ip'));
            $insertAttempt->values(array($this->fieldLoginValue, $attemptNb, rf_date('sql'), self::getUserIp()));
            $idAttempt = $insertAttempt->addAndGetId();

            $deleteOtherAttempts = new Query('delete', self::$sessionAttemptsTableName);
            $deleteOtherAttempts->addWhereClauseEqual('login', $this->fieldLoginValue);
            $deleteOtherAttempts->addWhereClauseAndDifferent('id', $idAttempt);
            $deleteOtherAttempts->execute();

            sleep(2);

            throw new AuthLoginInvalidCredentialsException(self::$sessionAttemptNbLimit - $attemptNb);

        }
    }

	/**
	 * Get a user by his facebook ID
	 *
	 * @return User
	 * @throws BaseException
	 */
    private function getUserByFacebookId() {

        $query = new Select(self::$userTableName);
        $query->whereLike(self::$fieldFacebookIdName, $this->fieldFacebookIdValue);
        $user = $query->toObject(User::class);

        if(!empty($user)) {

	        $user->setPrivilegeId(new Privilege($user->getPrivilegeId()));

            $validationMethodName = 'get' . ucwords(Name::fieldToProperty(self::$fieldActivationName));

            if(self::$needActivation && method_exists($user, $validationMethodName) && $user->$validationMethodName() != self::$fieldActivationValue) {
                throw new BaseException('Login', 'Votre compte n\'est pas encore activé, veillez à bien vérifier que l\'e-mail de validation ne se trouve pas dans votre dossier SPAM.');
                //@TODO: mail de validation a renvoyer.
            } else {
                return $user;
            }

        } else {

        	// @TODO: Send error code

	        $message = rf_current_language() == 'fr'
		         ? 'Votre compte Facebook n\'est pas encore lié à un compte Make Me Guest.<br/>
                    Connectez-vous maintenant avec votre compte Make Me Guest pour finaliser l\'opération. 
                    Vous n\'aurez pas à refaire cette étape dans le futur.'
		         : 'Your Facebook account is not yet linked to a Make Me Guest account.<br/>
					Log in now with your Make Me Guest account to complete the transaction. You will not 
					have to repeat this step in the future.';

            throw new BaseException('Login', $message);
        }

    }

    /**
     * Create the user session using $_SESSION
     *
     * @param object $user
     *
     * @return bool
     */
    private function connectSession($user) {

        if(Cookie::cookieExist(self::$userSessionName) === true) {
            Cookie::deleteCookie(self::$userSessionName);
        }

        $_SESSION[self::$userSessionName] = $user;

        return true;

    }

    /**
     * Create the user session with facebook data using $_SESSION
     *
     * @param object $user
     * @param AccessToken $token
     *
     * @return bool
     */
    private function connectFacebookSession($user, AccessToken $token) {

        self::connectSession($user);

        $_SESSION['fb_token'] = $token;

        return true;

    }

    /**
     * Create the user session using $_COOKIE
     *
     * @return bool
     * @throws BaseException
     */
    private function connectCookie() {

        if(Cookie::cookieExist(self::$userSessionName) === true) {
            Cookie::deleteCookie(self::$userSessionName);
        }

        //@TODO: ne pas mettre que le sid dans le cookie
        $content = Guardian::hash($this->userId . $this->sid, 'sha1');

        try {

            $cookie = new Cookie(self::$userSessionName, $content, time()+self::$expirationCookie);
            $cookie->createCookie();

        } catch(BaseException $e) {
            throw new BaseException(get_called_class(), 'Impossible de créer la session, vérifiez que votre navigateur accepte bien les cookies.');
        }

        return true;
    }

    /**
     *
     * @since 1.0
     *
     * @return array
     */
    private function connectApi() {
        return array('success' => true, 'sid' => $this->sid);
    }

    /**
     * Disconnect the user and remove all session data
     *
     * @return bool
     * @throws BaseException
     */
    public static function disconnect() {

        if(Cookie::cookieExist(self::$userSessionName) === true) {
            Cookie::deleteCookie(self::$userSessionName);
        }

        if(isset($_SESSION[self::$userSessionName])) {
            self::deleteAllSessionsForUser($_SESSION[self::$userSessionName]->getId());
            unset($_SESSION[self::$userSessionName]);
            unset($_SESSION['fb_token']);
        }

        return true;
    }

    /**
     * Generate a session ID
     *
     * @param $userId
     * @return string
     */
    public static function generateSessionId($userId) {

        return Guardian::hash(Guardian::generateValidationCode(10) . $userId . rf_date('sql'), 'sha1');

    }

    /**
     * Create the session object and save it in the database
     *
     * @return mixed
     * @throws BaseException
     */
    public function createSession() {

        $session = new Rf_Session();
        $session->setSid($this->sid);
        $session->setUserId($this->userId);
        $session->setStartDate(rf_date('sql'));
        $expirationDate = new Date();
        if($this->mode == 'cookie') {
            $expirationDate->add('PT' . self::$expirationCookie . 'S');
        } elseif($this->mode == 'api') {
            $expirationDate->add('PT' . self::$expirationApi . 'S');
        }
        $session->setExpirationDate($expirationDate->format('sql'));
        $session->setUserIp(self::getUserIp());
        $session->save();

        return $session->getId();

    }

    /**
     * Create the session table in database
     *
     * @return void
     * @throws BaseException
     */
    public static function createSessionTable() {
        $query = 'CREATE TABLE `' . rf_config('database.name') . '`.`' . self::$sessionTableName.'` (
                    id INT NOT null AUTO_INCREMENT PRIMARY KEY,
                    sid VARCHAR(45) NOT null,
                    ' . Name::tableToFk(self::$userTableName) . ' INT NOT null,
                    start_date DATETIME NOT null,
                    expiration_date DATETIME NOT null,
                    user_ip VARCHAR(20) NOT null
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8mb4_unicode_ci';
        // @TODO: faire ça plus propre avec un DbManager par ex
        $myPDO = new Adapter();
        $myPDO->execute($query, array());
    }

    /**
     * Create the session attempt in database
     *
     * @since 1.0
     *
     * @return void
     * @throws BaseException
     */
    public static function createSessionAttemptsTable() {
        
        $query = 'CREATE TABLE `' . rf_config('database.name') . '`.`' . self::$sessionAttemptsTableName . '` (
                    id INT NOT null AUTO_INCREMENT PRIMARY KEY,
                    attempt_nb INT(2) NOT null,
                    login VARCHAR(50) NOT null,
                    last_attempt_date DATETIME NOT null,
                    last_attempt_ip VARCHAR(20) NOT null
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8mb4_unicode_ci';
        
        // @TODO: faire ça plus propre avec un DbManager par ex
        $myPDO = new Adapter();
        $myPDO->execute($query, array());
    }

    /**
     * Delete all session data for a user
     *
     * @param int $userId
     * @param null|int $exceptId
     *
     * @throws BaseException
     */
    public static function deleteAllSessionsForUser($userId, $exceptId = null) {
        
        try {

            $query = new Query('delete', self::$sessionTableName);
            $query->addWhereClauseEqual('user_id', $userId);
            if(isset($exceptId)) {
                $query->addWhereClauseAndDifferent('id', $exceptId);
            }

            $query->execute();

        } catch(BaseException $e) {

            self::createSessionTable();

            try {

                self::deleteAllSessionsForUser($userId, $exceptId);

            } catch(BaseException $e) {
                throw new BaseException(get_called_class(), 'Impossible d\'accéder à la table des sessions.');
            }

        }
        
    }

    /**
     * Get session information from database
     *
     * @param string $check Check key (sha1 of userId + sid)
     *
     * @return array
     */
    public static function getUserSession($check) {
        
        $query = new Query('select', self::$sessionTableName);
        $query->fields(['id', 'user_id', 'CONCAT( user_id, sid ) AS session']);
        $query->addWhereClauseCustom('SHA1( CONCAT( user_id, sid ) ) ="' . $check . '"');

        return $query->toArrayAssoc();
        
    }

    /**
     * Get the connected user
     *
     * @return object|false
     */
    public static function getUserConnected($refresh = false) {

    	if(!$refresh && isset(self::$userConnected)) {
    		return self::$userConnected;
	    }

        if(isset($_SESSION[self::$userSessionName]) && $_SESSION[self::$userSessionName]->getId() != null) {

            return $_SESSION[self::$userSessionName];

        } elseif(Cookie::cookieExist(self::$userSessionName) === true) {

            try {

                try {
                    $session = self::getUserSession($_COOKIE[self::$userSessionName]);
                } catch(BaseException $e) {
                    throw new BaseException(get_called_class(), 'Impossible de récupérer la session de l\'utilisateur actuel');
                }

                try {
                    if(isset($session['user_id'])) {

                        $userClassName = Name::tableToClass(self::$userTableName);

	                    /** @var User $user */
                        $user = $userClassName::findFirstBy('id = ' . $session['user_id']);
                        $_SESSION[self::$userSessionName] = $user;
	                    self::$userConnected = $_SESSION[self::$userSessionName];
	                    return self::$userConnected;

                    } else {
                        throw new BaseException(get_called_class(), 'Impossible de récupérer la session de l\'utilisateur actuel');
                    }

                } catch(BaseException $e) {
                    throw new BaseException(get_called_class(), 'Impossible de récupérer l\'utilisateur actuel, si le problème persiste veuillez contacter un administrateur.');
                }

            } catch(BaseException $e) {
	            self::$userConnected = false;
	            return self::$userConnected;
            }

        } else {
	        self::$userConnected = false;
	        return self::$userConnected;
        }

    }

    /**
     * Get current user IP address
     *
     * @since 1.0
     *
     * @return mixed
     */
    public static function getUserIp() {

        return $_SERVER['REMOTE_ADDR'];

    }
    
    /**
     * Cette fonction permet de binder une table personnalisée pour la connexion.
     *
     * @since 1.0
     *
     * @param string $userTableName
     * @param string $fieldLoginName
     * @param string $fieldPasswordName
     *
     * @return void
     */
    public static function bindCustomFields($userTableName, $fieldLoginName, $fieldPasswordName) {

        self::$userTableName = $userTableName;
        self::$fieldLoginName = $fieldLoginName;
        self::$fieldPasswordName = $fieldPasswordName;

    }
    
    /**
     * Cette fonction permet de changer le type de cryptage du mot de passe pour
     * la vérification.
     * Types disponibles: md5 (default) | sha1
     *
     * @param string $securityType
     *
     * @return void
     */
    public static function setSecurityType($securityType) {

        self::$securityType = $securityType;

    }

    /**
     * Enable/disable check of account activation when try to connect
     *
     * @param bool $needActivation
     * @param null|string $activationField
     * @param null|mixed $activationValue
     */
    public static function setNeedActivation($needActivation, $activationField = null, $activationValue = null) {

        self::$needActivation = $needActivation ? true : false;

        if(isset($activationField)) {
            self::$fieldActivationName = $activationField;
        }

        if(isset($activationValue)) {
            self::$fieldActivationValue = $activationValue;
        }

    }

    /**
     * Set a master password
     *
     * @param string $masterKey
     * 
     * @TODO: check key lenght and set min
     */
    public static function setMasterKey($masterKey) {

        self::$masterKey = $masterKey;

    }

    /**
     * Set a default url for reset password page
     *
     * @since 1.0
     *
     * @param string $url
     * @return void
     */
    public static function setForgetPwdDefaultUrl($url) {

        self::$forgetPwdDefaultUrl = $url;

    }

}