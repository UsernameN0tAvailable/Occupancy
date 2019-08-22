<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class OccupancyDataController extends AbstractController
{
    /**
     * @Route("/occupancy_data/{location}/{date}", name="occupancy_data")
     */
    public function index(string $date, string $location)
    {

        $month = date("m",strtotime($date));

        $path = '/../../test_data/'.$location.'/'.$month.'/'.$date.'.json';

        $data = json_decode(file_get_contents(__DIR__ . $path), true);

        $timestamps = [];
        $occupancies = [];

        foreach ($data['data'] as $entry) {

            // not rly necessary
            $timestamp = new \DateTime($entry['datetime']['date']);
            $time = $timestamp->format("H:i");

            array_push($timestamps, $time);
            array_push($occupancies, $entry['occupancy']);
        }

        return $this->json([
            'labels' => $timestamps,
            'series' => [array('name' => 'occupancy', 'data' => $occupancies)]
        ]);
    }
}
