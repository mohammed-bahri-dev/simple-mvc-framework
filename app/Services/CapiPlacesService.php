<?php 
namespace Services;

class CapiPlacesService extends CapiService
{
    public function __construct($nomApp, $versionApp, $urlCallback, $cleConso, $secretConso)
    {
        parent::__construct($nomApp, $versionApp, $urlCallback, $cleConso, $secretConso); 
    }

    /**
     * Retourne les emplacements de l'API Places pour une entité donnée
     *
     * @param string $placesUrl
     * @param integer $entityId
     * @return stdClass
     */
    public function getPlaces($placesUrl, $entityId) 
    {
        $curl = curl_init();
        $options = $this->getPlacesOptions($placesUrl, $entityId);
        curl_setopt_array($curl, $options);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);

        $object = json_decode($response);
        curl_close($curl);
        
        if (!is_object($object)) {
            $this->setErrorMessage($err);
            return new \stdClass();
        } 
        
        if (!empty($object->error)) {
            $err = $object->error . " : " . $object->error_description;
            $this->setErrorMessage($err);
            return new \stdClass();
        }

        return $object;
    }

    /**
     * Retourne le tableau d'options d'interrogation de l'Api Places
     *
     * @param string $placesUrl
     * @param integer $entityId
     * @return array
     */
    public function getPlacesOptions($placesUrl, $entityId) 
    {
        $nom      = $this->nomApp;
        $version  = $this->versionApp;
        $bearer   = $this->getAccessToken();
        $correlationId = $this->getCorrelationId();

        $options = [
            CURLOPT_URL => $placesUrl . "/distribution_entities/" . $entityId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
//            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $bearer",
                "Content-Type: application/json",
                "Accept: application/json",
                "CorrelationID: $correlationId",
                "CATS_Consommateur: {\"consommateur\": {\"nom\": \"$nom\", \"version\": \"$version\"}}",
                "CATS_ConsommateurOrigine: {\"consommateur\": {\"nom\": \"$nom\", \"version\": \"$version\"}}",
                "CATS_Canal: {\"canal\": {\"canalId\": \"internet\", \"canalDistribution\": \"internet\"}}",
            ],
        ];

        if ($this->skipVerifyPeer) {
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = 2;
        }

        return $options;
    }
}
