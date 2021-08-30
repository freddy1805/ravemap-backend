<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeolocationService {

    const GEOLOCATION_API_URL = 'https://maps.googleapis.com/maps/api/geocode/json';
    const STATIC_MAP_API_URL = 'https://maps.googleapis.com/maps/api/staticmap';

    private string $mapsApiKey;

    private HttpClientInterface $httpClient;

    /**
     * GeolocationService constructor.
     * @param ContainerInterface $container
     * @param HttpClientInterface $httpClient
     */
    public function __construct(ContainerInterface $container, HttpClientInterface $httpClient)
    {
        $this->mapsApiKey = (string) $container->getParameter('maps_apiKey');
        $this->httpClient = $httpClient;
    }

    /**
     * @param array $location
     * @return string|false
     */
    public function getAdministrativeLevel(array $location)
    {
        $lat = $location['lat'];
        $long = $location['long'];

        $url = self::GEOLOCATION_API_URL . "?latlng=$lat,$long&sensor=true&key=$this->mapsApiKey";

        try {
            $administrativeLevel = '';
            $response = $this->httpClient->request('GET', $url);
            $data = $response->toArray();
            foreach ($data['results'] as $result) {
                if ($result['types'][0] === 'administrative_area_level_4') {
                    $administrativeLevel = $result['formatted_address'];
                }
            }
            return $administrativeLevel;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param array $location
     * @return false|string
     */
    public function getStaticMapImage(array $location)
    {
        $lat = $location['lat'];
        $long = $location['long'];

        $url = self::STATIC_MAP_API_URL . "?center=$lat,$long&zoom=10&scale=1&size=300x300&maptype=roadmap&format=png&visual_refresh=true&key=$this->mapsApiKey";

        try {
            $response = $this->httpClient->request('GET', $url);

            return $response->getContent();
        } catch (\Exception $exception) {
            throw $exception;
            return false;
        }
    }
}
