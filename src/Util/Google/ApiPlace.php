<?php

namespace App\Util\Google;

use App\Exceptions\GooglePlaces;
use App\Util\DotEnv;
use GuzzleHttp\Client;

class ApiPlace
{
    const NEARBYSEARCH = "https://maps.googleapis.com/maps/api/place/nearbysearch/json";

    const DETAILS = "https://maps.googleapis.com/maps/api/place/details/json";

    /** @var DotEnv $dotEnv */
    private $dotEnv;

    /** @var Client $client */
    private $client;

    public function __construct(DotEnv $dotEnv)
    {
        $this->dotEnv = $dotEnv;
        $this->client = new Client();
    }

    private function execute(string $url, string $method, array $options)
    {
        $response = $this->client->request($method, $url, $options);

        if (200 == $response->getStatusCode()) {
            return json_decode($response->getBody()->getContents(), true);
        }

        throw new GooglePlaces($response->getStatusCode());
    }

    public function nearbysearch(string $longitude, string $latitude, $type)
    {
        $response = $this->execute(self::NEARBYSEARCH, 'GET', [
            'query' => [
                'rankby' => 'distance',
                'location' => sprintf("%s,%s", $latitude, $longitude),
                'type' => $type,
                'key' => $this->dotEnv->load("GMAP_KEY"),
            ]
        ]);

        if (isset($response['status']) && "OK" == $response['status'] && isset($response['results'])) {
            return $response['results'];
        }

        return false;
    }

    public function details(string $placeId)
    {
        $response = $this->execute(self::DETAILS, 'GET', [
            'query' => [
                'place_id' => $placeId,
                'key' => $this->dotEnv->load("GMAP_KEY"),
            ]
        ]);

        if (isset($response['status']) && "OK" == $response['status'] && isset($response['result']) && $response['result']) {
            return $response['result'];
        }

        return ['failed' => false, 'response' => $response];
    }

    public function getDistanceBetweenTwoCoordinates(string $longitude, string $latitude, string $nearByLongitude, string $nearByLatitude)
    {
        if (($latitude == $nearByLongitude) && ($longitude == $nearByLatitude)) {
            return 0;
        }

        $distance = rad2deg(acos(sin(deg2rad($latitude)) * sin(deg2rad($nearByLatitude)) +  cos(deg2rad($latitude)) * cos(deg2rad($nearByLatitude)) * cos(deg2rad($longitude - $nearByLongitude))));

        return (($distance * 60 * 1.1515) * 1.609344 * 1000);
    }
}