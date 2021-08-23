<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends AbstractController {

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function indexAction()
    {
        return $this->render('Controller/Default/index.html.twig');
    }
}
