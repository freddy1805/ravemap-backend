<?php

namespace App\Controller;

use App\Service\Entity\EventManager;
use App\Service\GeolocationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class MapImageCacheController
 * @package App\Controller
 * @Route("/mapimage", name="ravemap_map_image_")
 */
class MapImageController extends AbstractController {

    private CacheInterface $cache;

    private GeolocationService $geolocationService;

    private EventManager $eventManager;

    /**
     * MapImageCacheController constructor.
     */
    public function __construct(GeolocationService $geolocationService, EventManager $eventManager)
    {
        $this->cache = new FilesystemAdapter();
        $this->geolocationService = $geolocationService;
        $this->eventManager = $eventManager;
    }

    /**
     * @Route("/{eventId}.png", name="event", methods={"GET"})
     */
    public function eventMapImageAction(string $eventId)
    {
        $event = $this->eventManager->getById($eventId);

        if (!$event) {
            throw new NotFoundHttpException('Event not found');
        }

        $cacheKey = 'map-'.$eventId;

        return $this->cache->get($cacheKey, function (ItemInterface $cacheItem) use ($event) {
            $cacheItem->expiresAfter(86400);
            if ($image = $this->geolocationService->getStaticMapImage($event->getLocation())) {
                $response = new Response($image, 200, [
                    'content-type' => 'image/png'
                ]);

                $response->setPublic();

                return $response;
            }
        });
    }

}
