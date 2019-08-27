<?php

namespace App\Controller;

use App\Repository\LocationRepository;
use App\Repository\MinuteEntryRepository;
use App\Services\JSONParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class DataController extends AbstractController
{
    /**
     * @Route("/data/{location}/{date}", name="data")
     */
    public function index(string $date, string $location, JSONParser $parser, MinuteEntryRepository $minuteEntryRepository, LocationRepository $locationRepository)
    {
        $labels = [];
        $data = [];
        $DB = true;
        $closing_h = 19;
        $closing_m = 0;
        $closing_dt = new \DateTime();
        $closing_dt->setTime($closing_h, $closing_m);
        $legend_name = "Besetzung";


        if ($DB) {

            $date = new \DateTime($date);
            $location = $locationRepository->findOneBy(['name' => $location]);
            $out = $minuteEntryRepository->findByDay($location, $date);

            foreach ($out as $entry) {
                array_push($labels, $entry['time']);
                array_push($data, $entry['occupancy']);
            }

        } else {
            $month = date("m", strtotime($date));
            $path = '/../../test_data/' . $location . '/' . $month . '/' . $date . '.json';
            $parser->setPath($path);
            $parser->parse();
            $labels = $parser->getTimeStamps();
            $data = $parser->getOccupancies();
        }

        // check if live render or not
        $last_av_dt = new \DateTime($date->format('Y-m-d') . ' ' . end($labels));
        $now = new \DateTime();


        if ($this->isLive($last_av_dt, $now, $closing_dt)) {

            $legend_name = "Besetzung (Live)";
            $interval = new \DateInterval('PT' . 1 . 'M');

            while($last_av_dt <= $closing_dt){
                $last_av_dt->add($interval);
                array_push($labels, $last_av_dt->format('H:i:s'));
            }
        }


        return $this->json([
            'labels' => $labels,
            'series' => [array('name' => $legend_name, 'data' => $data)]
        ]);

    }

    /**
     * @Route("/range_stats/{location}/{from}/{to}", name="range_stats")
     */
    public function rangeStats(string $location, string $from, string $to, MinuteEntryRepository $minuteEntryRepository, LocationRepository $locationRepository)
    {

        $location = $locationRepository->findOneBy(['name' => $location]);
        $from = new \DateTime($from . ' 08:00:00');
        $to = new \DateTime($to . ' 18:00:00');
        $data = $minuteEntryRepository->findByLocationInRange($location, $from, $to);

        // data to pass as json to Chartist
        $timestamps = [];
        $max = [];
        $min = [];
        $avg = [];

        foreach ($data as $entry) {
            array_push($timestamps, $entry['time']);
            array_push($max, $entry['max']);
            array_push($min, $entry['min']);
            array_push($avg, $entry['avg']);
        }

        return $this->json([
            'labels' => $timestamps,
            'series' => [array('name' => 'maximum', 'data' => $max), array('name' => 'durchschnitt', 'data' => $avg), array('name' => 'minimum', 'data' => $min)]
        ]);
    }

    /**
     * @Route("/week_day_range_stats/{location}/{from}/{to}", name="week_day_range_stats")
     */
    public function weekDayRangeStats(string $location, string $from, string $to, MinuteEntryRepository $minuteEntryRepository, LocationRepository $locationRepository)
    {

        $weekDays = array('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag');

        $location = $locationRepository->findOneBy(['name' => $location]);
        $from = new \DateTime($from . ' 08:00:00');
        $to = new \DateTime($to . ' 18:00:00');
        $data = $minuteEntryRepository->findDailyStatsInRange($location, $from, $to);

        // data to pass as json to Chartist
        $timestamps = [];
        $max = [];
        $min = [];
        $avg = [];

        foreach ($data as $entry) {

            array_push($timestamps, $weekDays[$entry['weekday']]);
            array_push($max, $entry['max']);
            array_push($min, $entry['min']);
            array_push($avg, $entry['avg']);
        }

        return $this->json([
            'labels' => $timestamps,
            'series' => [array('name' => 'maximum', 'data' => $max), array('name' => 'durchschnitt', 'data' => $avg), array('name' => 'minimum', 'data' => $min)]
        ]);
    }

    private function isLive(\DateTime $last_av_dt, \DateTime $now, \DateTime $closing_dt)
    {
        return
            $last_av_dt->format('Y-m-d') == $now->format('Y-m-d')
            && $closing_dt > $last_av_dt;
    }


}
