<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DataController extends AbstractController
{
    /**
     * @Route("/data/", name="data")
     */
    public function index()
    {
        $data = json_decode(file_get_contents(__DIR__ . '/../../example.json'), true);

        $result = ['label' => [], 'series' => []];

        foreach($data['data'] as $entry){
            //$timestamp = new \DateTime($entry['datetime']['date']);

            //dump($timestamp->format('H:i'));
            //exit;
        }

        return $this->json([
            'labels' => [
                'Mon',
                'Tue',
                'Wed',
                'Thu',
                'Fri',
                'Sat',
                'Sun',
            ],
            'series' => [
                [5, 2, 10, 2, 0, 0, 20],

            ]

        ]);
    }
}
