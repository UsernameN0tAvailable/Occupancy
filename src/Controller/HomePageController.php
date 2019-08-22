<?php


namespace App\Controller;

use Symfony\Component\HttpFundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomePageController extends AbstractController {

    /**
     * @Route("/", methods={"GET"})
     */

    public function home(){
        return $this->render('homepage.html.twig');
    }
}