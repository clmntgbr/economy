<?php

namespace App\MessageHandler\Gas;

use App\Entity\Gas\Station;
use App\Message\Gas\CreateGasStation;
use App\Repository\Gas\StationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateGasStationHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var StationRepository */
    private $stationRepository;

    public function __construct(EntityManagerInterface $em, StationRepository $stationRepository)
    {
        $this->em = $em;
        $this->stationRepository = $stationRepository;
    }

    public function __invoke(CreateGasStation $message)
    {
        if (!$this->em->isOpen()) {
            $this->em = $this->em->create($this->em->getConnection(), $this->em->getConfiguration());
        }

        $station = $this->stationRepository->findOneBy(['id' => $message->getStationId()]);

        if ($station instanceof Station) {
            return;
        }

        $station = new Station(
            $message->getStationId(),
            $message->getPop(),
            $message->getCp(),
            $message->getLongitude(),
            $message->getLatitude(),
            $message->getStreet(),
            $message->getCity(),
            $message->getCountry(),
            $message->getElement()
        );

        $this->getGasStationById($station);

        $this->em->persist($station);
        $this->em->flush();
    }

    public function getGasStationById(Station $station)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => sprintf("https://www.prix-carburants.gouv.fr/map/recupererInfosPdv/%s", $station->getId()),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authority: www.prix-carburants.gouv.fr",
                "content-length: 0",
                "accept: text/javascript, text/html, application/xml, text/xml, */*",
                "x-prototype-version: 1.7",
                "x-requested-with: XMLHttpRequest",
                "user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36",
                "content-type: application/x-www-form-urlencoded; charset=UTF-8",
                "origin: https://www.prix-carburants.gouv.fr",
                "sec-fetch-site: same-origin",
                "sec-fetch-mode: cors",
                "sec-fetch-dest: empty",
                "referer: https://www.prix-carburants.gouv.fr/",
                "accept-language: fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7,pt;q=0.6,de-DE;q=0.5,de;q=0.4,ru;q=0.3,vi;q=0.2,la;q=0.1,es;q=0.1",
                "cookie: PHPSESSID=74qmi76d5k6vk4uhal69k0qhf6; device_view=full; cookie_law=true; device_view=full"
            ),
        ));

        $html = curl_exec($curl);

        curl_close($curl);

        if(false === $html) {
            return false;
        }

        $values = trim(strip_tags(str_replace("\n", '/break/', $html)));
        $values = explode('/break/', $values);
        $values = array_map('trim', $values);
        $values = array_filter($values);

        if (isset($values[4]) && isset($values[5]) && isset($values[6]) && isset($values[7])) {
            $station
                ->setName(trim($values[4]))
                ->setCompany(trim($values[5]))
                ->setStreet(trim($values[6]))
                ->setCity(trim($values[7]));
        }

        return $station;
    }
}