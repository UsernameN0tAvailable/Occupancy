<?php

namespace App\Controller;

use App\Repository\LocationRepository;
use App\Repository\MinuteEntryRepository;
use App\Services\JSONParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
        if ($DB) {

            $location = $locationRepository->findOneBy(['name' => $location]);
            $date = new \DateTime($date);

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

        return $this->json([
            'labels' => $labels,
            'series' => [array('name' => 'occupancy', 'data' => $data)]
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

            //array_push($max, ($entry['max']/$location->getMaxOccupancy()));
            //array_push($min, ($entry['min']/$location->getMaxOccupancy()));
            //array_push($avg, ($entry['avg']/$location->getMaxOccupancy()));
        }

        return $this->json([
            'labels' => $timestamps,
            'series' => [array('name' => 'average', 'data' => $avg), array('name' => 'max', 'data' => $max), array('name' => 'min', 'data' => $min)]
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
            'series' => [array('name' => 'max', 'data' => $max), array('name' => 'average', 'data' => $avg), array('name' => 'min', 'data' => $min)]
        ]);
    }



}
