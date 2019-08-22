<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class InOutDataController extends AbstractController
{
    /**
     * @Route("/in_out_data", name="in_out_data")
     */
    public function index()
    {
        $data = json_decode(file_get_contents(__DIR__ . '/../../example.json'), true);

        $timestamps = [];
        $prev_in = 0;
        $prev_out = 0;
        $delta = [];

        foreach ($data['data'] as $entry) {

            // not rly necessary
            $timestamp = new \DateTime($entry['datetime']['date']);
            $time = $timestamp->format("H:i");
            $minutes = intval($timestamp->format("i"));

            if(!($minutes % 30)) {
                array_push($timestamps, $time);

                $curr_in = $entry['total_in'];
                $curr_out = $entry['total_out'];

                array_push($delta, ($curr_in - $prev_in) -($curr_out - $prev_out));

                $prev_in = $curr_in;
                $prev_out = $curr_out;
            }
        }


        return $this->json([
            'labels' => $timestamps,
            'series' => [array('name' => 'in', 'data' => $delta)]
        ]);
    }
}
