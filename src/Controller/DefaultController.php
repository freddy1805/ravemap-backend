<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends AbstractController {

    private ContainerInterface $serviceContainer;

    /**
     * DefaultController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->serviceContainer = $container;
    }

    /**
     * @Route(name="index", methods={"GET"})
     */
    public function indexAction(Request $request): RedirectResponse
    {
        $selectedLocale = $this->serviceContainer->getParameter('locale');

        if ($userLocale = $request->getLocale()) {
            $selectedLocale = $userLocale;
        }

        return $this->redirectToRoute('localized_index', [
            '_locale' => $selectedLocale
        ]);
    }
}
