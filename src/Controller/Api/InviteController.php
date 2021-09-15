<?php

namespace App\Controller\Api;

use App\Service\Entity\EventManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * Class InviteController
 * @package App\Controller\Api
 * @Route("/invite", name="ravemap_api_invite_")
 */
class InviteController extends BaseApiController {

    /**
     * @var EventManager
     */
    private EventManager $eventManager;

    /**
     * InviteController constructor.
     * @param ContainerInterface $container
     * @param EventManager $eventManager
     */
    public function __construct(ContainerInterface $container, EventManager $eventManager)
    {
        parent::__construct($container);
        $this->eventManager = $eventManager;
    }

    /**
     * @OA\Delete(
     *     operationId="delete",
     *     summary="Delete invite by id",
     *     tags={"Invite"},
     *     @OA\Response(response="200", description="Returns json object with removal status"),
     *     @OA\Response(response="401", description="Login faild. Invalid credentials")
     * )
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function deleteInviteAction(string $id)
    {

    }
}
