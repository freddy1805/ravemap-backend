<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionListener
{
    /**
     * Route prefix for api routes
     */
    private const API_PREFIX = 'ravemap_api_';

    /**
     * Define all api exceptions here
     */
    private const API_EXCEPTIONS = [
        NotFoundHttpException::class => [
            'status' => 404
        ],
        BadRequestHttpException::class => [
            'status' => 400
        ],
    ];

    /**
     * Returns json exception for api endpoints
     * based on thrown exception
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $route = $event->getRequest()->get('_route');

        if (strpos($route, self::API_PREFIX) !== false) {
            foreach (self::API_EXCEPTIONS as $EXCEPTION_CLASS => $API_EXCEPTION) {
                if ($exception instanceof $EXCEPTION_CLASS) {
                    $event->setResponse(new JsonResponse([
                        'code' => $API_EXCEPTION['status'],
                        'message' => $exception->getMessage()
                    ], $API_EXCEPTION['status']));
                    return;
                }
            }

            if ($exception instanceof \Exception) {
                $event->setResponse(new JsonResponse([
                    'code' => 500,
                    'message' => 'An error occured'
                ], 500));
            }
        }
    }
}
