<?php

namespace App\Controller\Api;

use App\Entity\Event;
use App\Service\Entity\EventManager;
use App\Service\Entity\UserManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class EventController
 * @package App\Controller\Api
 * @Route("/event", name="ravemap_api_event_")
 */
class EventController extends BaseApiController {

    private EventManager $eventManager;

    private UserManager $userManager;

    /**
     * EventController constructor.
     * @param ContainerInterface $container
     * @param EventManager $eventManager
     * @param UserManager $userManager
     */
    public function __construct(ContainerInterface $container, EventManager $eventManager, UserManager $userManager)
    {
        parent::__construct($container);
        $this->eventManager = $eventManager;
        $this->userManager = $userManager;
    }

    /**
     * @OA\Get(
     *     operationId="detail",
     *     summary="Get detailed event data",
     *     tags={"Event"},
     *     @OA\Response(response="200", description="Returns json object with detailed event data"),
     *     @OA\Response(response="401", description="Login faild. Invalid credentials")
     * )
     * @Route("/{id}", name="detail", methods={"GET"})
     * @param string $id
     * @return Response
     */
    public function detailAction(string $id): Response
    {
        $event = $this->eventManager->getById($id);

        if (!$event) {
            throw new NotFoundHttpException('Event not found');
        }

        return new Response($this->serializeToJson($event, ['event_detail', 'user_list']), 200, [
            'content-type' => self::JSON_CONTENT_TYPE
        ]);
    }

    /**
     * @OA\Get(
     *     operationId="getLocation",
     *     summary="Get detailed event location data",
     *     tags={"Event"},
     *     @OA\Response(response="200", description="Returns json object with detailed event location data"),
     *     @OA\Response(response="401", description="Login faild. Invalid credentials")
     * )
     * @Route("/{id}/location", name="location", methods={"GET"})
     * @param string $id
     * @return Response
     */
    public function locationAction(string $id): Response
    {
        $event = $this->eventManager->getById($id);

        if (!$event) {
            throw new NotFoundHttpException('Event not found');
        }

        return new Response($this->serializeToJson($event, ['event_location']), 200, [
            'content-type' => self::JSON_CONTENT_TYPE
        ]);
    }

    /**
     * @OA\Get(
     *     operationId="getInvites",
     *     summary="Get detailed event invite data",
     *     tags={"Event"},
     *     @OA\Response(response="200", description="Returns json object with detailed event invites data"),
     *     @OA\Response(response="401", description="Login faild. Invalid credentials")
     * )
     * @Route("/{id}/invites", name="invites", methods={"GET"})
     * @param string $id
     * @return Response
     */
    public function invitesAction(string $id): Response
    {
        $event = $this->eventManager->getById($id);

        if (!$event) {
            throw new NotFoundHttpException('Event not found');
        }

        return new Response($this->serializeToJson($event, ['event_invites', 'invite_detail', 'user_list']), 200, [
            'content-type' => self::JSON_CONTENT_TYPE
        ]);
    }

    /**
     * @OA\Post(
     *     operationId="create",
     *     summary="Create a new event",
     *     tags={"Event"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              title="CreateEventObject",
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Rave at Cave"),
     *              @OA\Property(property="date", type="string", example="2021-10-10T20:00:00+02:00"),
     *              @OA\Property(property="eventMode", type="integer", example=0),
     *              @OA\Property(property="maxInvites", type="integer", example=100),
     *              @OA\Property(property="description", type="string", example="Lorem ipsum dolor sit amet...."),
     *              @OA\Property(property="approval", type="boolean", example=false),
     *              @OA\Property(
     *                  property="location",
     *                  type="object",
     *                  @OA\Property(property="lat", type="string", example="51.586444128449564"),
     *                  @OA\Property(property="long", type="string", example="7.403728958819874")
     *              )
     *         )
     *     ),
     *     @OA\Response(response="201", description="Returns the new created event"),
     *     @OA\Response(response="401", description="Login faild. Invalid credentials")
     * )
     * @Route("/new", name="create", methods={"POST"})
     */
    public function createAction(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $data['creator'] = $this->getUser();

        $event = $this->eventManager->create($data, true);

        return new Response($this->serializeToJson($event, ['event_detail', 'user_list']), 200, [
            'content-type' => self::JSON_CONTENT_TYPE
        ]);
    }

    /**
     * @OA\Post(
     *     operationId="createInvite",
     *     summary="Create a new invite in event",
     *     tags={"Event"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              title="CreateEventInviteObject",
     *              type="object",
     *              @OA\Property(property="role", type="string", example="moderator", nullable=false),
     *              @OA\Property(property="toUser", type="string", example="a2c70ebc-0698-11ec-a225-7c05070d28b8", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response="201", description="Returns the new created invite"),
     *     @OA\Response(response="401", description="Login faild. Invalid credentials")
     * )
     * @Route("/{id}/invite", name="create_invite", methods={"POST"})
     */
    public function createInviteAction(string $id, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $data['fromUser'] = $this->getUser();

        if (isset($data['toUser'])) {
            $data['toUser'] = $this->userManager->getById($data['toUser']);
        }

        if ($event = $this->eventManager->createInvite($id, $data)) {
            $event->setUrl(
                $this->generateUrl('ravemap_invite_detail', [
                    'id' => $event->getId()
                ], UrlGeneratorInterface::ABSOLUTE_URL)
            );

            return new Response($this->serializeToJson($event, ['invite_detail', 'invite_url', 'user_list', 'event_list']), 200, [
                'content-type' => self::JSON_CONTENT_TYPE
            ]);
        }

        throw new BadRequestHttpException();
    }


    /**
     * @OA\Delete (
     *     operationId="removeEvent",
     *     summary="Remove an event",
     *     tags={"Event"},
     *     @OA\Response(response="201", description="Returns the removal status"),
     *     @OA\Response(response="401", description="Login faild. Invalid credentials")
     * )
     * @Route("/{id}", name="remove", methods={"DELETE"})
     */
    public function removeEventAction(string $id): Response
    {
        /** @var Event $event */
        $event = $this->eventManager->getById($id);

        if (!$event) {
            throw new NotFoundHttpException('Event not found');
        }

        if ($event->getCreator() !== $this->getUser()) {
            throw new AuthenticationException('You are not the owner of this event');
        }

        return new Response($this->serializeToJson([
            'success' => $this->eventManager->remove($event),
        ], ['remove_event']), 200, [
            'content-type' => self::JSON_CONTENT_TYPE,
        ]);
    }
}
