<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\Entity\EventManager;
use App\Service\Entity\UserManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * Class UserController
 * @package App\Controller\Api
 * @Route("/user", name="reavemap_api_user_")
 */
class UserController extends BaseApiController {

    private UserManager $userManager;

    private EventManager $eventManager;

    /**
     * UserController constructor.
     * @param ContainerInterface $container
     * @param UserManager $userManager
     * @param EventManager $eventManager
     */
    public function __construct(ContainerInterface $container, UserManager $userManager, EventManager $eventManager)
    {
        parent::__construct($container);
        $this->userManager = $userManager;
        $this->eventManager = $eventManager;
    }

    /**
     * @OA\Get(
     *     operationId="me",
     *     summary="Get detailed user data",
     *     tags={"User"},
     *     @OA\Response(response="200", description="Returns json object with detailed user data"),
     *     @OA\Response(response="401", description="Login faild. Invalid credentials")
     * )
     * @Route("/me", name="me", methods={"GET"})
     */
    public function meAction(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $user->setCreatedEvents(
            $this->eventManager->getByCreator($user)
        );

        return new Response($this->serializeToJson($user, ['user_detail', 'invite_list', 'event_list', 'event_location']), 200, [
            'content-type' => self::JSON_CONTENT_TYPE
        ]);
    }

    /**
     * @OA\Post(
     *     operationId="updateMe",
     *     summary="Update user data",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              title="UpdateUserObject",
     *              type="object",
     *              @OA\Property(property="username", type="string", example="freddy"),
     *              @OA\Property(property="email", type="string", example="freddy@test.de"),
     *         )
     *     ),
     *     @OA\Response(response="200", description="Returns the updated user"),
     *     @OA\Response(response="401", description="Login faild. Invalid credentials")
     * )
     * @Route("/me/update", name="update_me", methods={"POST"})
     */
    public function updateMeAction(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $updatedUser = $this->userManager->update($this->getUser(), $data, true);

        return new Response($this->serializeToJson($updatedUser, ['user_detail', 'invite_list', 'event_list']), 200, [
            'content-type' => self::JSON_CONTENT_TYPE
        ]);
    }
}
