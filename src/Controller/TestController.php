<?php


namespace App\Controller;

use Symfony\Component\HttpFundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController {

    /**
     * @Route("/test", methods={"GET"})
     */

    public function test(){



        return $this->render('test.html.twig');






    }



}