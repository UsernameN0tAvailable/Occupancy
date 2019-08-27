<?php

namespace App\Command;

use App\Entity\Location;
use App\Entity\MinuteEntry;
use App\Repository\LocationRepository;
use App\Services\JSONParser;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';

    private $manager;
    private $jsonParser;

    public function __construct(EntityManagerInterface $manager, JSONParser $JSONParser)
    {

        parent::__construct(self::$defaultName);
        $this->manager = $manager;
        $this->jsonParser = $JSONParser;
    }

    protected function configure()
    {
        $this
            ->setDescription('Updates occupancy data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$locations = ['bmu_og', 'bmu_ug', 'bto', 'jbb', 'von_roll'];
        $locations = ['bmu_ug'];
        $base_path = '/../../test_data/';


        $io = new SymfonyStyle($input, $output);

        /** @var LocationRepository $locationRepo */
        $locationRepo = $this->manager->getRepository(Location::class);

        $flushCount = 0;

        $cache = new ArrayCache();

        foreach($locations as $location){
            $location = $locationRepo->findOneBy(['name' => $location]);



            // for each day in the month
            for($i = 27; $i <= 27; $i++){



                $day = $i;

                if ($i < 10){
                    $day = '0'.$i;
                }


                $path = $base_path.$location.'/08/2019-08-'.$day.'.json';

                $this->jsonParser = new JSONParser();
                $this->jsonParser->setPath($path);
                $this->jsonParser->parse();

                $dateTimes = $this->jsonParser->getDateTimes();
                $occupancies = $this->jsonParser->getOccupancies();
                $total_ins = $this->jsonParser->getTotalIns();
                $total_outs = $this->jsonParser->getTotalOuts();

                //dump($dateTimes);
               // exit;

                $counter = 0;

                foreach($dateTimes as $dt){

                    $minuteEntry = new MinuteEntry();

                    $minuteEntry->setDateTime($dt);
                    $minuteEntry->setOccupancy($occupancies[$counter]);
                    $minuteEntry->setTotalIn($total_ins[$counter]);
                    $minuteEntry->setTotalOut($total_outs[$counter]);
                    $minuteEntry->setLocation($location);

                    $counter++;

                    $this->manager->persist($minuteEntry);

                }

                if($flushCount % 100 == 0){
                    $this->manager->flush();
                    $cache->deleteAll();
                }

                $flushCount++;
            }
            $io->success($location.' imported!!');
        }

        $this->manager->flush();


        $io->success('Looks like we re done without errors... !!!!');
    }

    private function importDay()
    {


    }


}
