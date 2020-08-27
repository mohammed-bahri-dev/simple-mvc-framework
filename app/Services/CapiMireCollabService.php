<?php 
namespace Services;

class CapiMireCollabService extends CapiService
{
    public function __construct($nomApp, $versionApp, $urlCallback, $cleConso, $secretConso)
    {
        parent::__construct($nomApp, $versionApp, $urlCallback, $cleConso, $secretConso); 
    }

        /**
     * Redirection vers la Mire d'Authentification
     *
     * @param string $urlMire
     * @param string $urlCallback
     * @param string $state
     * @param integer $scopes
     * @return void
     */
    public function mireLogin($mireUrl, $scopes)
    {
        $url = $this->getMireLoginUrl($mireUrl, $scopes);
        $this->redirect($url);
    }

    /**
     * Redirection vers la Mire de Logout sans affichage
     *
     * @param string $mireUrl
     * @param string $callbackUrl
     * @return void
     */
    public function mireLogoutExpress($mireUrl, $callbackUrl)
    {
        $url = $this->getMireLogoutExpressUrl($mireUrl, $callbackUrl);
        $this->redirect($url);
    }

    /**
     * Retourne l'url de la Mire de Logout sans affichage
     *
     * @param string $mireUrl
     * @param string $callbackUrl
     * @return string
     */
    public function getMireLogoutExpressUrl($mireUrl, $callbackUrl)
    {
        $url = $mireUrl . '/loggedout';
        return $url . '?goto=' . urlencode($callbackUrl);
    }

    /**
     * Retourne l'Url de login de la mire
     *
     * @param string $mireUrl
     * @param string $scopes
     * @return string
     */
    public function getMireLoginUrl($mireUrl, $scopes)
    {
        $scope = $this->getScopes($scopes);
        $state = $this->getState();

        $url = $mireUrl . '/authorize';
        $url .= '?response_type=code';
        $url .= '&client_id=' . $this->cleConso;
        $url .= '&redirect_uri=' . urlencode($this->urlCallback);
        $url .= '&scope=' . $scope;
        return $url . '&state=' . $state;
    }

    /**
     * Redirection vers la page authentifiée ou page d'erreur
     * Récupère et stoque le access token
     *
     * @return void
     */
    public function mireCallback($urlSuccess, $urlFailure)
    {
        // ### Retour en erreur
        $error = $this->getGet('error');
        if (!empty($error)) {
            $errorDescription = $this->getGet('error_description');
            // Ajoute le message d'erreur en session
            $this->setErrorMessage($errorDescription);
            // Redirige vers la page d'erreur, message récupérable en session
            $this->redirect($urlFailure);
        }

        // ### Retour page autorisée
        // Vérifie le CSRF
        $state = $this->getGet('state');
        if (!$this->isStateValid($state)) {
            // Ajoute le message d'erreur en session
            $this->setErrorMessage("CSRF Token invalide");
            // Redirige vers la page d'erreur, message récupérable en session
            $this->redirect($urlFailure);
        }

        // Stocke le access token 
        $code = $this->getGet('code');
        $this->setCodeAuth($code);

        // Redirige vers la page success
        $this->redirect($urlSuccess);
    }

}
