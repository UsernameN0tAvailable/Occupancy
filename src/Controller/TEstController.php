<?php

namespace App\Controller;

use App\Repository\LocationRepository;
use App\Repository\MinuteEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TEstController extends AbstractController
{
    /**
     * @Route("/test", name="t_est")
     */
    public function index(MinuteEntryRepository $repo, LocationRepository $locRepo)
    {
        $loc = $locRepo->findOneBy(['name' => 'bmu_og']);

        $from = new \DateTime('2019-07-01 08:00:00');
        $to = new \DateTime('2019-07-05 18:00:00');


        $ret = $repo->findByLocationInRange($loc,$from, $to );


        dump($ret);
        exit;

        return $this->render('t_est/index.html.twig', [
            'controller_name' => 'TEstController',
        ]);
    }
}
