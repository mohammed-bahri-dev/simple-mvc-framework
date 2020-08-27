<?php
namespace Controllers;

use Core\View;
use Core\Controller;
use Services\CapiMireCollabService;
use Services\CapiService;

/*
 * Welcome controller
 *
 * @author David Carr - dave@daveismyname.com - http://daveismyname.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 */
class WelcomeController extends Controller
{
    /**
     * Define Index page title and load template files
     */
    public function index()
    {
        $data['title'] = "Démonstrateur d'Authentification CAPI";

        $scopes = CapiService::SCOPE_OPEN_ID | CapiService::SCOPE_PROFIL | CapiService::SCOPE_FUNCTIONNAL_POSTS;
        $capiMireService = new CapiMireCollabService(CAPI_NOM_APP, CAPI_VERSION_APP, CAPI_MIRE_CALLBACK_URL, CAPI_CLE_CONSOMMATEUR, CAPI_SECRET_CONSOMMATEUR);
        $data['mireUrl'] = $capiMireService->getMireLoginUrl(CAPI_MIRE_URL, $scopes);

        View::renderTemplate('header', $data);
        View::render('welcomeView', $data);
        View::renderTemplate('footer', $data);
    }

}
