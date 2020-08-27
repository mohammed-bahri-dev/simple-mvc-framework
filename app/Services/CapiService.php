<?php
namespace Services;

use DateTime;
use Throwable;

class CapiService
{
    /** CONSTANTES pour les Sessions */
    const SESSION_CSRF_KEY  = "CAPI_CSRF";
    const SESSION_CSRF_TIME = "CAPI_TIME";
    const SESSION_ERROR_MSG = "CAPI_MESSAGE";
    const SESSION_CODE_AUTH = "CAPI_CODE_AUTH";
    const SESSION_CORRELATION_ID = "CAPI_CORRELATION_ID";

    /** CONSTANTES pour définir le Scope */
    const SCOPES = "scopes";
    const SCOPE_OPEN_ID = 1;
    const SCOPE_PROFIL  = 2; 
    const SCOPE_FUNCTIONNAL_POSTS = 4;

    /** Default Correlation ID */
    const CORRELATION_ID_DEFAULT = "95d493e2-5457-4350-9ce9-c3a1d18f4eff";

    /** AUC9 */
    const SESSION_ACCESS_TOKEN = "CAPI_ACCESS_TOKEN";
    const SESSION_ID_TOKEN = "CAPI_ID_TOKEN";
    const SESSION_REFRESH_TOKEN = "CAPI_REFRESH_TOKEN";
    const SESSION_JWT = "CAPI_JWT";
    const SESSION_JWT_PAYLOAD = "CAPI_JWT_PAYLOAD";
    const SESSION_JWT_MATRICULE = "CAPI_JWT_MATRICULE";
    const SESSION_JWT_NOM = "CAPI_JWT_NOM";
    const SESSION_JWT_PRENOM = "CAPI_JWT_PRENOM";
    const SESSION_JWT_EMAIL = "CAPI_JWT_EMAIL";
    const SESSION_JWT_STRUCTURE_ID = "CAPI_JWT_STRUCTURE_ID";
    const SESSION_JWT_EXPIRE_TIMESTAMP = "CAPI_JWT_EXPIRE_TIMESTAMP";

    /** PARAMETRES de l'Application */
    protected $nomApp;
    protected $versionApp;
    protected $urlCallback;
    protected $cleConso;
    protected $secretConso;  

    protected $skipVerifyPeer = false;

    /**
     * Constructeur du Service
     *
     * @param string $nomApp
     * @param string $versionApp
     * @param string $cleConso
     * @param string $secretConso
     */
    public function __construct($nomApp, $versionApp, $urlCallback, $cleConso, $secretConso)
    {
        $this->nomApp      = $nomApp;
        $this->versionApp  = $versionApp;
        $this->urlCallback = $urlCallback;
        $this->cleConso    = $cleConso;
        $this->secretConso = $secretConso;    
    }

    /**
     * A utiliser uniquement en dev : 
     * Permet d'ouvrir un flux TLS malgré l'absence de certificat sur le serveur de dev
     *
     * @return void
     */
    public function skipVerifyPeer()
    {
        $this->skipVerifyPeer = true;
    }

    /**
     * Retourne l'access token de connection
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->retreive(self::SESSION_ACCESS_TOKEN);
    }

    /**
     * Stocke en session l'access token
     *
     * @param string $token
     * @return void
     */
    protected function setAccessToken($token)
    {
        $this->persist(self::SESSION_ACCESS_TOKEN, $token);
    }

    /**
     * Retourne l'id token de connection
     *
     * @return string
     */
    public function getIdToken()
    {
        return $this->retreive(self::SESSION_ID_TOKEN);
    }

    /**
     * Stock en session l'id token
     *
     * @param string $token
     * @return void
     */
    protected function setIdToken($token)
    {
        $this->persist(self::SESSION_ID_TOKEN, $token);
    }

    /**
     * Retourne le tableau d'options CURL pour l'affichage
     *
     * @param array $options
     * @return array
     */
    public function printCurlOptions(array $options)
    {
        return [
            "CURLOPT_URL" => $options[CURLOPT_URL],
            "CURLOPT_RETURNTRANSFER" => $options[CURLOPT_RETURNTRANSFER] ? "true" : "false",
            "CURLOPT_ENCODING" => $options[CURLOPT_ENCODING],
            "CURLOPT_MAXREDIRS" => $options[CURLOPT_MAXREDIRS],
            "CURLOPT_TIMEOUT" => $options[CURLOPT_TIMEOUT],
            "CURLOPT_FOLLOWLOCATION" => $options[CURLOPT_FOLLOWLOCATION] ? "true" : "false",
            "CURLOPT_HTTP_VERSION" => $options[CURLOPT_HTTP_VERSION],
            "CURLOPT_CUSTOMREQUEST" => $options[CURLOPT_CUSTOMREQUEST],
            "CURLOPT_POSTFIELDS" => $options[CURLOPT_POSTFIELDS],
            "CURLOPT_HTTPHEADER" => $options[CURLOPT_HTTPHEADER]
        ];
    }

    /**
     * Retourne le message d'erreur d'authetification stocké en session
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->pull(self::SESSION_ERROR_MSG);
    }

    protected function setErrorMessage($msg)
    {
        $this->persist(self::SESSION_ERROR_MSG, $msg);
    }

    /**
     * Retourne le code d'autorisation
     *
     * @return string
     */
    public function getCodeAuth()
    {
        return $this->retreive(self::SESSION_CODE_AUTH);
    }

    /**
     * Stock en session le code d'autorisation
     *
     * @param string $code
     * @return void
     */
    protected function setCodeAuth($code)
    {
        $this->persist(self::SESSION_CODE_AUTH, $code);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getRefreshToken()
    {
        return $this->retreive(self::SESSION_REFRESH_TOKEN);
    }

    /**
     * Stock en session le refresh Token
     *
     * @param string $token
     * @return void
     */
    protected function setRefreshToken($token)
    {
        $this->persist(self::SESSION_REFRESH_TOKEN, $token);
    }

    /** 
     * Retoune le jeton JWT
     * @return string
     */
    public function getJwtToken()
    {
        return $this->retreive(self::SESSION_JWT);
    }

    /**
     * Retourne la partie payload du JWT 
     *
     * @return string
     */
    public function getJwtPayload()
    {
        return $this->retreive(self::SESSION_JWT_PAYLOAD);
    }

    /**
     * Retourne le matricule dans le JWT
     *
     * @return string
     */
    public function getJWtMatricule()
    {
        return $this->retreive(self::SESSION_JWT_MATRICULE);
    }

    /**
     * Retourne le prenom dans le JWT
     *
     * @return string
     */
    public function getJWtPrenom()
    {
        return $this->retreive(self::SESSION_JWT_PRENOM);
    }

    /**
     * Retourne le nom dans le JWT
     *
     * @return string
     */
    public function getJWtNom()
    {
        return $this->retreive(self::SESSION_JWT_NOM);
    }

    /**
     * Retourne l'email dans le JWT
     *
     * @return string
     */
    public function getJWtEmail()
    {
        return $this->retreive(self::SESSION_JWT_EMAIL);
    }

    /**
     * Retourne l'id structure dans le JWT
     *
     * @return string
     */
    public function getJWtStructureId()
    {
        return $this->retreive(self::SESSION_JWT_STRUCTURE_ID);
    }

    /**
     * Retourne le timestamp d'expiration du jeton JWT
     *
     * @return integer
     */
    public function getJWtExpireTimestamp()
    {
        $timestamp = $this->retreive(self::SESSION_JWT_EXPIRE_TIMESTAMP);
        if (empty($timestamp)) {
            return "";
        }
        return $timestamp;
    }

    /**
     * Retourne le Date Time d'expiration du Jeton JWT
     *
     * @return string
     */
    public function getJWtExpireDateTime()
    {
        $timestamp = $this->getJWtExpireTimestamp();
        if (empty($timestamp)) {
            return "";
        }
        $date = new DateTime();
        try {
            $date->setTimestamp($timestamp);
        }
        catch(Throwable $e) {
            // Ne rien faire ...
        }
        return $date->format("Y-m-d H:i:s");
    }

    /**
     * Persiste le Timestamp de validité de l'Access Token
     *
     * @param integer $timestamp
     * @return void
     */
    protected function setJwtExpireTimestamp($timestamp)
    {
        $this->persist(self::SESSION_JWT_EXPIRE_TIMESTAMP, $timestamp);
    }

    /**
     * Stock en session le jeton JWT
     *
     * @param string $jwt
     * @return void
     */
    protected function setJwtToken($jwt)
    {
        $this->persist(self::SESSION_JWT, $jwt);
        if (empty($jwt)) {
            $this->clearJwtSession();
            return;
        }

        // Extract Payload
        preg_match("/^\w+\.(\w+)\./", $jwt, $matches);
        $jwtPayload = isset($matches[1]) ? base64_decode($matches[1]) : null;
        $this->persist(self::SESSION_JWT_PAYLOAD, $jwtPayload);

        // Extract Collab infos
        $payload = json_decode($jwtPayload);
        if (empty($payload)) {
            $this->clearJwtSession();
            return;
        }

        $this->persist(self::SESSION_JWT_MATRICULE,         $payload->sub);
        $this->persist(self::SESSION_JWT_NOM,               $payload->family_name);
        $this->persist(self::SESSION_JWT_PRENOM,            $payload->given_name);
        $this->persist(self::SESSION_JWT_EMAIL,             $payload->email);
        $this->persist(self::SESSION_JWT_STRUCTURE_ID,      $payload->structure_id);
        $this->persist(self::SESSION_JWT_EXPIRE_TIMESTAMP,  $payload->exp);
    }

    /**
     * Supprimer les element de session du jeton JWT 
     */
    protected function clearJwtSession()
    {
        $this->forget(self::SESSION_JWT_MATRICULE);
        $this->forget(self::SESSION_JWT_NOM);
        $this->forget(self::SESSION_JWT_PRENOM);
        $this->forget(self::SESSION_JWT_EMAIL);
        $this->forget(self::SESSION_JWT_STRUCTURE_ID);
        $this->forget(self::SESSION_JWT_EXPIRE_TIMESTAMP);
    }

    /**
     * Génère le State pour la CSRF et le stocke en Session
     *
     * @return string
     */
    public function getState()
    {
        $max_time = 60 * 60 * 24; // token is valid for 1 day

        $csrf_token  = $this->retreive(self::SESSION_CSRF_KEY);
        $stored_time = $this->retreive(self::SESSION_CSRF_TIME);
        
        if ($max_time + $stored_time <= time() || empty($csrf_token)) {
            $csrf = md5( uniqid(rand(), true) );
            $this->persist(self::SESSION_CSRF_KEY, $csrf);
            $this->persist(self::SESSION_CSRF_TIME, time() );
        }
        
        return $this->retreive(self::SESSION_CSRF_KEY);
    }

    /**
     * checks if CSRF token in session is same as in the form submitted
     *
     * @param $state
     * @return bool
     */
    public function isStateValid($state)
    {
        return $this->retreive(self::SESSION_CSRF_KEY) == $state;
    }

    /**
     * Vérifie que le token est toujours valide
     * Il reste au moins 10 secondes
     *
     * @return boolean
     */
    public function isTokenValid()
    {
        $date = new DateTime();
        $timestamp = $date->getTimestamp() - 10; // Il reste au moins 10 secondes
        return $this->getJWtExpireTimestamp() < $timestamp;
    }

    /**
     * Retourne le Scope pour les API
     *
     * @param int $scopes
     * @return string
     */
    public function getScopes($scopes)
    {
        $scope = "openid"; // par défaut au minimum
        if ($scopes & self::SCOPE_PROFIL == 2) {
            $scope .= "%20profile";
        }
        if ($scopes & self::SCOPE_FUNCTIONNAL_POSTS == 4) {
            $scope .= "%20functionnal_posts";
        }
        return $scope;
    }

    /**
     * Sauvegarde en session l'id de correlation
     *
     * @param string $correlationId
     * @return void
     */
    public function setCorrelationId($correlationId)
    {
        $this->persist(self::SESSION_CORRELATION_ID, $correlationId);
    }

    public function getCorrelationId()
    {
        return $this->retreive(self::SESSION_CORRELATION_ID, self::CORRELATION_ID_DEFAULT);        
    }

    /**
     * Retourne le code d'authorisation
     *
     * @return string
     */
    public function getAuthorization()
    {
        return base64_encode($this->cleConso . ":" . $this->secretConso);
    }

    /**
     * Récupère le paramètre passé en Get
     * Sanitize la donnée pour prévenir des failles XSS
     *
     * @param string $name
     * @param string $default
     * @return void
     */
	public function getGet($name, $default = "") {
		if (array_key_exists($name, $_GET)) {
			$get = $_GET[$name];
			if (is_string($get)) {
				$get = $this->sanitize($get);
            }
			return $get;
		}
		return $default;
    }

     /**
     * Récupère le paramètre passé en Post
     * Sanitize la donnée pour prévenir des failles XSS
     *
     * @param string $name
     * @param string $default
     * @return void
     */
	public function getPost($name, $default = "") {
		if (array_key_exists($name, $_POST)) {
			$get = $_POST[$name];
			if (is_string($get)) {
				$get = $this->sanitize($get);
            }
			return $get;
		}
		return $default;
    }
    
    /**
     * Prevent XSS injection
     *
     * @param string $value
     * @return string
     */ 
    public function sanitize($value)
    {
		return strip_tags( trim($value) );
    }

    /**
     * Redirige vers une Url
     *
     * @param string $url
     * @return void
     */
    public function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Persist Data en Session
     * Actuellement en Session mais peut être modifié pour persister en DB, Redis, JWT, etc.
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function persist($key, $value)
    {
        $_SESSION[SESSION_PREFIX.$key] = $value;
    }

    /**
     * Oublie un Data en Session
     *
     * @param string $key
     * @return void
     */
    public function forget($key)
    {
        unset($_SESSION[SESSION_PREFIX.$key]);
    }

    /**
     * Retourne une donnée persistée en Session
     *
     * @param string $key
     * @param mixed $default
     * @return string
     */
    public function retreive($key, $default = null)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return $default;
    }

    /**
     * Retourne et oublie une donnée persisté en session
     *
     * @param string $key
     * @return string
     */
    public function pull($key)
    {
        $value = $this->retreive($key);
        $this->forget($key);
        return $value;
    }

}
