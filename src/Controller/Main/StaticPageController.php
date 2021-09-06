<?php

namespace App\Controller\Main;

use App\Service\Entity\LocaleManager;
use App\Service\Entity\StaticPageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StaticPageController
 * @package App\Controller\Main
 * @Route(name="ravemap_static_")
 */
class StaticPageController extends AbstractController {

    private LocaleManager $localeManager;

    private StaticPageManager $staticPageManager;

    private MessageBusInterface $messageBus;

    /**
     * StaticPageController constructor.
     * @param LocaleManager $localeManager
     * @param StaticPageManager $staticPageManager
     * @param MessageBusInterface $messageBus
     */
    public function __construct(LocaleManager $localeManager, StaticPageManager $staticPageManager, MessageBusInterface $messageBus)
    {
        $this->localeManager = $localeManager;
        $this->staticPageManager = $staticPageManager;
        $this->messageBus = $messageBus;
    }

    /**
     * StaticPageController constructor.
     * @Route("/{slug}", name="page_by_slug", methods={"GET"})
     */
    public function pageBySlugAction(Request $request, string $slug): Response
    {
        if ($locale = $this->localeManager->getLocaleByIdentifier($request->getLocale())) {
            $page = $this->staticPageManager->getPageByLocaleAndSlug($locale, $slug);

            return $this->render('Controller/StaticPage/page_by_slug.html.twig', [
                'page' => $page,
                'background' => false
            ]);
        }
        throw new NotFoundHttpException('Page not found');
    }
}
