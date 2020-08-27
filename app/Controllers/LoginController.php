<?php
namespace Controllers;

use Core\Controller;
use Core\View;
use Services\CapiAuc9Service;
use Services\CapiMireCollabService;
use Services\CapiPlacesService;
use Services\CapiService;

class LoginController extends Controller
{
    public function mireLogin() 
    {
        $scopes = CapiService::SCOPE_OPEN_ID | CapiService::SCOPE_PROFIL | CapiService::SCOPE_FUNCTIONNAL_POSTS;
        $capiMireService = new CapiMireCollabService(CAPI_NOM_APP, CAPI_VERSION_APP, CAPI_MIRE_CALLBACK_URL, CAPI_CLE_CONSOMMATEUR, CAPI_SECRET_CONSOMMATEUR);
        $capiMireService->mireLogin(CAPI_MIRE_URL, $scopes);
    }

    public function mireCallback()
    {
        $capiMireService = new CapiMireCollabService(CAPI_NOM_APP, CAPI_VERSION_APP, CAPI_MIRE_CALLBACK_URL, CAPI_CLE_CONSOMMATEUR, CAPI_SECRET_CONSOMMATEUR);
        $capiMireService->mireCallback('auth', 'error');
    }

    public function mireAuth()
    {
        $data['title'] = "Démonstrateur d'Authentification CAPI";

        $scopes = CapiService::SCOPE_OPEN_ID | CapiService::SCOPE_PROFIL | CapiService::SCOPE_FUNCTIONNAL_POSTS;
        $capiAuc9Service = new CapiAuc9Service(CAPI_NOM_APP, CAPI_VERSION_APP, CAPI_MIRE_CALLBACK_URL, CAPI_CLE_CONSOMMATEUR, CAPI_SECRET_CONSOMMATEUR);
        $options = $capiAuc9Service->getAuc9TokenOptions(CAPI_AUC9_URL, $scopes);

        $data['code']    = $capiAuc9Service->getCodeAuth();
        $data['options'] = $capiAuc9Service->printCurlOptions($options);

        $data['error_msg'] = $capiAuc9Service->getErrorMessage();

        View::renderTemplate('header', $data);
        View::render('authView', $data);
        View::renderTemplate('footer', $data);
    }

    public function mireLogout() 
    {
        $data['title'] = "Démonstrateur d'Authentification CAPI";

        $capiAuc9Service = new CapiAuc9Service(CAPI_NOM_APP, CAPI_VERSION_APP, CAPI_MIRE_CALLBACK_URL, CAPI_CLE_CONSOMMATEUR, CAPI_SECRET_CONSOMMATEUR);
        $options = $capiAuc9Service->getAuc9RevokeOptions(CAPI_AUC9_URL, DIR);
        $data['options'] = $capiAuc9Service->printCurlOptions($options);

        $data['error_msg'] = $capiAuc9Service->getErrorMessage();

        View::renderTemplate('header', $data);
        View::render('logoutView', $data);
        View::renderTemplate('footer', $data);
    }

    public function auc9Token() 
    {
        $scopes = CapiService::SCOPE_OPEN_ID | CapiService::SCOPE_PROFIL | CapiService::SCOPE_FUNCTIONNAL_POSTS;
        $capiAuc9Service = new CapiAuc9Service(CAPI_NOM_APP, CAPI_VERSION_APP, CAPI_MIRE_CALLBACK_URL, CAPI_CLE_CONSOMMATEUR, CAPI_SECRET_CONSOMMATEUR);
        $capiAuc9Service->auc9Token(CAPI_AUC9_URL, $scopes);
        $capiAuc9Service->redirect(DIR . 'member');
    }

    public function auc9Refresh()
    {
        $capiAuc9Service = new CapiAuc9Service(CAPI_NOM_APP, CAPI_VERSION_APP, CAPI_MIRE_CALLBACK_URL, CAPI_CLE_CONSOMMATEUR, CAPI_SECRET_CONSOMMATEUR);
        $capiAuc9Service->auc9Refresh(CAPI_AUC9_URL);
        $capiAuc9Service->redirect(DIR . 'member');
    }

    public function auc9Revoke()
    {
        $capiAuc9Service = new CapiAuc9Service(CAPI_NOM_APP, CAPI_VERSION_APP, CAPI_MIRE_CALLBACK_URL, CAPI_CLE_CONSOMMATEUR, CAPI_SECRET_CONSOMMATEUR);
        $capiAuc9Service->auc9Revoke(CAPI_AUC9_URL, DIR);
    }

    public function member()
    {
        $data['title'] = "Démonstrateur d'Authentification CAPI";

        // Info AUC9
        $capiService = new CapiService(CAPI_NOM_APP, CAPI_VERSION_APP, CAPI_MIRE_CALLBACK_URL, CAPI_CLE_CONSOMMATEUR, CAPI_SECRET_CONSOMMATEUR);
        $data['idToken']      = $capiService->getIdToken();
        $data['accessToken']  = $capiService->getAccessToken();
        $data['refreshToken'] = $capiService->getRefreshToken();
        $data['jwtToken']     = $capiService->getJwtToken();
        $data['jwtPayload']   = $capiService->getJwtPayload();

        $data['matricule']    = $capiService->getJWtMatricule();
        $data['nom']          = $capiService->getJWtNom();
        $data['prenom']       = $capiService->getJWtPrenom();
        $data['email']        = $capiService->getJWtEmail();
        $data['structure']    = $capiService->getJWtStructureId();
        $data['expire']       = $capiService->getJWtExpireDateTime();

        // API Places
        $capiPlacesService = new CapiPlacesService(CAPI_NOM_APP, CAPI_VERSION_APP, CAPI_MIRE_CALLBACK_URL, CAPI_CLE_CONSOMMATEUR, CAPI_SECRET_CONSOMMATEUR);
        $placesOptions = $capiPlacesService->getPlacesOptions(CAPI_PLACES_URL, 2);
        $data['placesOptions'] = $capiPlacesService->printCurlOptions($placesOptions);

        // API AUC9 refresh
        $capiAuc9Service = new CapiAuc9Service(CAPI_NOM_APP, CAPI_VERSION_APP, CAPI_MIRE_CALLBACK_URL, CAPI_CLE_CONSOMMATEUR, CAPI_SECRET_CONSOMMATEUR);
        $refreshOption = $capiAuc9Service->auc9RefreshOptions(CAPI_AUC9_URL);
        $data['refreshOptions'] = $capiAuc9Service->printCurlOptions($refreshOption);

        // Mire Logout
        $capiMireService = new CapiMireCollabService(CAPI_NOM_APP, CAPI_VERSION_APP, CAPI_MIRE_CALLBACK_URL, CAPI_CLE_CONSOMMATEUR, CAPI_SECRET_CONSOMMATEUR);
        $data['mireUrl'] = $capiMireService->getMireLogoutExpressUrl(CAPI_MIRE_URL, DIR . 'logout');

        $data['error_msg'] = $capiMireService->getErrorMessage();

        View::renderTemplate('header', $data);
        View::render('memberView', $data);
        View::renderTemplate('footer', $data);
    }

    public function places()
    {
        $data['title'] = "Démonstrateur d'Authentification CAPI";

        $capiPlacesService = new CapiPlacesService(CAPI_NOM_APP, CAPI_VERSION_APP, CAPI_MIRE_CALLBACK_URL, CAPI_CLE_CONSOMMATEUR, CAPI_SECRET_CONSOMMATEUR);
        $entityId = $capiPlacesService->getPost('entityId');
        $data['places']  = $capiPlacesService->getPlaces(CAPI_PLACES_URL, $entityId);

        $data['error_msg'] = $capiPlacesService->getErrorMessage();

        View::renderTemplate('header', $data);
        View::render('placesView', $data);
        View::renderTemplate('footer', $data);
    }

    public function error()
    {
        $data['title'] = "Démonstrateur d'Authentification CAPI";
        $capiService = new CapiService(CAPI_NOM_APP, CAPI_VERSION_APP, CAPI_MIRE_CALLBACK_URL, CAPI_CLE_CONSOMMATEUR, CAPI_SECRET_CONSOMMATEUR);
        $data['message'] = $capiService->getErrorMessage();

        View::renderTemplate('header', $data);
        View::render('error/loginFailureView', $data);
        View::renderTemplate('footer', $data);
    }

}
