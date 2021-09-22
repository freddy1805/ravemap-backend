<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Exception\ValidationException;
use App\Message\UserRegisteredMessage;
use App\Service\Entity\UserManager;
use FOS\UserBundle\Util\TokenGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * Class AuthorizationController
 * @package App\Controller\Api
 * @Route(name="ravemap_api_auth_")
 */
class AuthorizationController extends BaseApiController
{
    /**
     * @var UserManager
     */
    private UserManager $userManager;

    /**
     * @var MessageBusInterface
     */
    private MessageBusInterface $messageBus;

    /**
     * AuthorizationController constructor.
     * @param ContainerInterface $container
     * @param UserManager $userManager
     * @param MessageBusInterface $messageBus
     */
    public function __construct(
        ContainerInterface $container,
        UserManager $userManager,
        MessageBusInterface $messageBus
    ) {
        parent::__construct($container);
        $this->userManager = $userManager;
        $this->messageBus = $messageBus;
    }

    /**
     * @OA\Post(
     *     operationId="login",
     *     security={},
     *     summary="Login by username and password to get JWT",
     *     tags={"Authorization"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              title="LoginObject",
     *              type="object",
     *              @OA\Property(property="username", type="string", example="USERNAME"),
     *              @OA\Property(property="password", type="string", example="**********"),
     *         )
     *
     *     ),
     *     @OA\Response(response="200", description="Returns json object with token and a refreshToken, which can be used in authorization-header"),
     *     @OA\Response(response="401", description="Login faild. Invalid credentials")
     * )
     * @Route("/login_check", name="login", methods={"POST"})
     */
    public function loginAction(): Response
    {
        return $this->redirectToRoute('api_login_check');
    }


    /**
     * @OA\Post(
     *     operationId="refreshToken",
     *     security={},
     *     summary="Send refresh-token to renew JWT",
     *     tags={"Authorization"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              title="RefreshTokenObject",
     *              type="object",
     *              @OA\Property(property="refresh_token", type="string", example="refresh-token")
     *         )
     *
     *     ),
     *     @OA\Response(response="200", description="Returns json object with token and a refreshToken, which can be used in authorization-header"),
     *     @OA\Response(response="401", description="Renew failed. Check refresh_token")
     * )
     * @Route("/token/refresh", name="token_refresh", methods={"POST"})
     * @return Response
     */
    public function refreshAction(): Response
    {
        return $this->forward('gesdinet.jwtrefreshtoken::refresh');
    }

    /**
     * @OA\Post(
     *     operationId="checkUsername",
     *     security={},
     *     summary="Check username availability",
     *     tags={"Authorization"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              title="CheckUsernameAvailabilityObject",
     *              type="object",
     *              @OA\Property(property="username", type="string", example="max_mustermann")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Returns json object with available indecator"),
     * )
     * @Route("/check", name="check_username", methods={"POST"})
     */
    public function checkUsernameAction(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['username'])) {
            throw new BadRequestHttpException('No username found');
        }

        return new JsonResponse([
            'available' => $this->userManager->isUsernameAvailable($data['username']),
        ]);

    }

    /**
     * @OA\Post(
     *     operationId="register",
     *     security={},
     *     summary="Register a new account",
     *     tags={"Authorization"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              title="RegistrationObject",
     *              type="object",
     *              @OA\Property(property="username", type="string", example="max_mustermann"),
     *              @OA\Property(property="email", type="string", example="max@mustermann.de"),
     *              @OA\Property(property="plainPassword", type="string", example="Strong-*-1234-Password")
     *         )
     *     ),
     *     @OA\Response(response="201", description="Returns json object with new user"),
     *     @OA\Response(response="400", description="Bad request! Check payload")
     * )
     * @Route("/register", name="register", methods={"POST"})
     * @throws ValidationException
     */
    public function registerAction(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        /** @var User $user */
        $user = $this->userManager->create($data, true);

        $this->messageBus->dispatch(new UserRegisteredMessage($user));

        return new Response($this->serializeToJson($user, ['user_detail']), 201, [
            'content-type' => self::JSON_CONTENT_TYPE
        ]);
    }

    /**
     * @OA\Post(
     *     operationId="confirmAccount",
     *     security={},
     *     summary="Confirm user account with token",
     *     tags={"Authorization"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              title="ConfirmObject",
     *              type="object",
     *              @OA\Property(property="token", type="string", example="3u3ehukdnw3qscaD§Cw")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Returns success indecator"),
     *     @OA\Response(response="400", description="Bad request! Check payload")
     * )
     * @Route("/confirm", name="confirm_account", methods={"POST"})
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function confirmAction(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['token'])) {
            if ($user = $this->userManager->getByConfirmationToken($data['token'])) {
                $user->setEnabled(true);
                $user->setConfirmationToken(null);
                return new JsonResponse([
                    'success' => $this->userManager->save($user)
                ]);
            }

            throw new NotFoundHttpException('result.not_found');
        }

        throw new BadRequestHttpException('data.not_valid');
    }
}
