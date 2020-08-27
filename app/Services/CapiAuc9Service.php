<?php 
namespace Services;

use DateTime;

class CapiAuc9Service extends CapiService
{
    public function __construct($nomApp, $versionApp, $urlCallback, $cleConso, $secretConso)
    {
        parent::__construct($nomApp, $versionApp, $urlCallback, $cleConso, $secretConso); 
    }

        /**
     * Demande des Token AUC9
     *
     * @param string $auc9Url
     * @param string $scopes
     * @return void
     */
    public function auc9Token($auc9Url, $scopes)
    {
        $curl = curl_init();
        $options = $this->getAuc9TokenOptions($auc9Url, $scopes);
        curl_setopt_array($curl, $options);
        
        $response = curl_exec($curl);
        $object = json_decode($response);

        $this->setAccessToken(null);
        $this->setIdToken(null);
        $this->setJwtToken(null);
        $this->setRefreshToken(null);
        
        if (!is_object($object)) {
            $err = curl_error($curl);
            $this->setErrorMessage($err);
        } 
        elseif (!empty($object->error)) {
            $err = $object->error . " : " . $object->error_description;
            $this->setErrorMessage($err);
        }
        else {
            $this->setAccessToken($object->access_token);
            $this->setRefreshToken($object->refresh_token);
            $this->setIdToken($object->id_token);
            $this->setJwtToken($object->id_token);
        } 

        curl_close($curl);
    }

    /**
     * Retourne les Options Curl de la demande des Token AUC9
     *
     * @param string $auc9Url
     * @param string $scopes
     * @return array
     */
    public function getAuc9TokenOptions($auc9Url, $scopes)
    {
        $scope    = $this->getScopes($scopes);
        $code     = $this->getCodeAuth();
        $callback = urlencode($this->urlCallback);
        $nom      = $this->nomApp;
        $version  = $this->versionApp;
        $auth     = $this->getAuthorization();
        $correlationId = $this->getCorrelationId();

        $options = [
            CURLOPT_URL => $auc9Url . "/openid/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "grant_type=authorization_code&code=$code&redirect_uri=$callback&scope=$scope&authentication_level=2",
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic $auth",
                "Accept: application/json",
                "CorrelationID: $correlationId",
                "CATS_Consommateur: {\"consommateur\": {\"nom\": \"$nom\", \"version\": \"$version\"}}",
                "CATS_ConsommateurOrigine: {\"consommateur\": {\"nom\": \"$nom\", \"version\": \"$version\"}}",
                "CATS_Canal: {\"canal\": {\"canalId\": \"internet\", \"canalDistribution\": \"internet\"}}",
                "Content-Type: application/x-www-form-urlencoded"
            ],
        ];

        if ($this->skipVerifyPeer) {
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = 2;
        }

        return $options;
    }

    public function auc9Refresh($auc9Url)
    {
        $curl = curl_init();
        $options = $this->auc9RefreshOptions($auc9Url);
        curl_setopt_array($curl, $options);
          
        $response = curl_exec($curl);
        $object = json_decode($response);
        
        if (!is_object($object)) {
            $err = curl_error($curl);
            $this->setErrorMessage($err);
        } 
        elseif (!empty($object->error) || !empty($object->errorDescription)) {
            $err = $object->error . " : " . $object->errorDescription;
            $this->setErrorMessage($err);
        }
        else {
            // Met à jour le Timestamp de l'access token (9 minutes : 540s)
            $now = new DateTime();
            $timestamp = $now->getTimestamp() + 540;
            $this->setJwtExpireTimestamp($timestamp);
            
            // Met à jour l'access token
            $this->setAccessToken($object->access_token);
        } 

        curl_close($curl);
    }

    public function auc9RefreshOptions($auc9Url)
    {
        $scope    = "openid";
        $nom      = $this->nomApp;
        $version  = $this->versionApp;
        $auth     = $this->getAuthorization();
        $refreshToken = $this->getRefreshToken();
        $correlationId = $this->getCorrelationId();

        $options = [
            CURLOPT_URL => $auc9Url . "/openid/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>
                "grant_type=refresh_token" . 
                "&scope=$scope" .
                "&authentication_level=2" .
                "&refresh_token=$refreshToken",
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic $auth",
                "Accept: application/json",
                "CorrelationID: $correlationId",
                "CATS_Consommateur: {\"consommateur\": {\"nom\": \"$nom\", \"version\": \"$version\"}}",
                "CATS_ConsommateurOrigine: {\"consommateur\": {\"nom\": \"$nom\", \"version\": \"$version\"}}",
                "CATS_Canal: {\"canal\": {\"canalId\": \"internet\", \"canalDistribution\": \"internet\"}}",
                "Content-Type: application/x-www-form-urlencoded",
                "cats_environnementapi: reference",
            ],
        ];

        if ($this->skipVerifyPeer) {
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = 2;
        }

        return $options;
    }

    /**
     * Revoke les Token d'AUC9 pour Logout
     *
     * @param string $auc9Url
     * @param string $callbackUrl
     * @return void
     */
    public function auc9Revoke($auc9Url, $callbackUrl)
    {
        $curl = curl_init();
        $options = $this->getAuc9RevokeOptions($auc9Url);
        curl_setopt_array($curl, $options);
          
        $response = curl_exec($curl);
        $object = json_decode($response);
        
        if (!is_object($object)) {
            $err = curl_error($curl);
            $this->setErrorMessage($err);
        } 
        elseif (!empty($object->error)) {
            $err = $object->error . " : " . $object->error_description;
            $this->setErrorMessage($err);
        }

        curl_close($curl);

        $this->redirect($callbackUrl);
    }

    /**
     * Retourne les Options Curl de la demande des Token AUC9
     *
     * @param string $auc9Url
     * @return array
     */
    public function getAuc9RevokeOptions($auc9Url)
    {
        $nom      = $this->nomApp;
        $version  = $this->versionApp;
        $auth     = $this->getAuthorization();
        $refreshToken = $this->getRefreshToken();
        $correlationId = $this->getCorrelationId();
        
        $options = [
            CURLOPT_URL => $auc9Url . "/openid/revoke",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "token=$refreshToken",
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic $auth",
                "Accept: application/json",
                "CorrelationID: $correlationId",
                "CATS_Consommateur: {\"consommateur\": {\"nom\": \"$nom\", \"version\": \"$version\"}}",
                "CATS_ConsommateurOrigine: {\"consommateur\": {\"nom\": \"$nom\", \"version\": \"$version\"}}",
                "CATS_Canal: {\"canal\": {\"canalId\": \"internet\", \"canalDistribution\": \"internet\"}}",
                "Content-Type: application/x-www-form-urlencoded",
            ],
        ];

        if ($this->skipVerifyPeer) {
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = 2;
        }

        return $options;
    }

}
