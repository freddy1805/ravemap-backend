<?php

namespace App\Controller\Api;

use App\Entity\Invite;
use App\Service\Entity\EventManager;
use App\Service\Entity\InviteManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @var InviteManager
     */
    private InviteManager $inviteManager;

    /**
     * InviteController constructor.
     * @param ContainerInterface $container
     * @param EventManager $eventManager
     * @param InviteManager $inviteManager
     */
    public function __construct(ContainerInterface $container, EventManager $eventManager, InviteManager $inviteManager)
    {
        parent::__construct($container);
        $this->eventManager = $eventManager;
        $this->inviteManager = $inviteManager;
    }

    /**
     * @OA\Get(
     *     operationId="detail",
     *     summary="Get detailed invite data",
     *     tags={"Invite"},
     *     @OA\Response(response="200", description="Returns json object with detailed invite data"),
     *     @OA\Response(response="401", description="Login faild. Invalid credentials")
     * )
     * @Route("/{id}", name="detail", methods={"GET"})
     */
    public function detailAction(string $id): Response
    {
        /** @var Invite $invite */
        if ($invite = $this->inviteManager->getById($id)) {
            if ($invite->getToUser() === null) {
                return new Response($this->serializeToJson($invite, ['invite_detail', 'user_list', 'event_list']), 200, [
                    'content-type' => self::JSON_CONTENT_TYPE,
                ]);
            }
        }

        throw new NotFoundHttpException('Invite not found');
    }

    /**
     * @OA\Post(
     *     operationId="accept",
     *     summary="Accept invite with authenticated user",
     *     tags={"Invite"},
     *     @OA\Response(response="200", description="Returns json object with detailed invite data and success indicator"),
     *     @OA\Response(response="401", description="Login faild. Invalid credentials")
     * )
     * @Route("/{id}/accept", name="accept", methods={"POST"})
     */
    public function acceptAction(string $id): Response
    {
        /** @var Invite $invite */
        if ($invite = $this->inviteManager->getById($id)) {
            $invite->setToUser($this->getUser());
            $invite->setStatus(Invite::STATUS_INVITE_ACCEPTED);

            return new Response($this->serializeToJson([
                'success' =>  $this->inviteManager->save($invite),
                'invite' => $invite
            ], ['invite_detail', 'event_list', 'user_list']), 200, [
                'content-type' => self::JSON_CONTENT_TYPE,
            ]);
        }

        throw  new NotFoundHttpException('Invite not found');
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
