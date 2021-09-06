<?php

namespace App\Controller\Main;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocalizedController extends AbstractController {

    /**
     * @Route(name="localized_index", methods={"GET"})
     */
    public function localizedAction(Request $request): Response
    {
        return $this->render('Controller/Default/index.html.twig', [
            'background' => false
        ]);
    }
}
