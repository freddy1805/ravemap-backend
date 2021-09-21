<?php

namespace App\Controller\Api;

use App\Entity\Media;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Service\Entity\DeviceManager;
use App\Service\Entity\EventManager;
use App\Service\Entity\UserManager;
use Sonata\MediaBundle\Entity\MediaManager;
use Sonata\MediaBundle\Form\Type\ApiMediaType;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Json;

/**
 * Class UserController
 * @package App\Controller\Api
 * @Route("/user", name="reavemap_api_user_")
 */
class UserController extends BaseApiController
{

    private UserManager $userManager;

    private EventManager $eventManager;

    private DeviceManager $deviceManager;

    private MediaManagerInterface $mediaManager;

    private FormFactoryInterface $formFactory;

    private Pool $mediaPool;

    private string $tempUserImagePath;

    /**
     * UserController constructor.
     * @param ContainerInterface $container
     * @param UserManager $userManager
     * @param EventManager $eventManager
     * @param DeviceManager $deviceManager
     * @param FormFactoryInterface $factory
     * @param Pool $mediaPool
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(
        ContainerInterface $container,
        UserManager $userManager,
        EventManager $eventManager,
        DeviceManager $deviceManager,
        FormFactoryInterface $factory,
        Pool $mediaPool,
        ParameterBagInterface $parameterBag
    )
    {
        parent::__construct($container);
        $this->userManager = $userManager;
        $this->eventManager = $eventManager;
        $this->deviceManager = $deviceManager;
        $this->formFactory = $factory;
        $this->mediaManager = $container->get('sonata.media.manager.media');
        $this->mediaPool = $mediaPool;
        $this->tempUserImagePath = $parameterBag->get('kernel.project_dir') . '/var/temp';
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

    /**
     * @OA\Post(
     *     operationId="updateImage",
     *     summary="Update user image",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              title="UpdateUserImageObject",
     *              type="object",
     *              @OA\Property(property="base64", type="string", example="base64 image string"),
     *         )
     *     ),
     *     @OA\Response(response="200", description="Returns the updated user"),
     *     @OA\Response(response="401", description="Login faild. Invalid credentials")
     * )
     * @Route("/me/image", name="update_image", methods={"POST"})
     */
    public function updateImageAction(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $base64 = $data['base64'];

            if (empty($base64)) {
                throw new BadRequestHttpException('base64 image not found');
            }

            $user = $this->getUser();

            if ($file = $this->base64ToJpeg($base64)) {
                $media = $this->saveImageMediaBundle($file, $user);

                $this->userManager->update($user, [
                    'image' => $media
                ], true);
            }

            return new Response($this->serializeToJson($user, ['user_detail']), 200, [
                'content-type' => self::JSON_CONTENT_TYPE
            ]);
        } catch (\Exception $ex ) {
            return new Response($this->serializeToJson(['error' => 'Coud not upload image'], ['upload_error']), 400, [
                'content-type' => self::JSON_CONTENT_TYPE
            ]);
        }
    }

    /**
     * @OA\Post(
     *     operationId="addFriend",
     *     summary="Add friend to own user",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              title="AddFriendObject",
     *              type="object",
     *              @OA\Property(property="user-id", type="string", example="3782b-23ji2h-iji23-3434p"),
     *         )
     *     ),
     *     @OA\Response(response="200", description="Returns the updated user"),
     *     @OA\Response(response="401", description="Login faild. Invalid credentials")
     * )
     * @Route("/friend/add", name="add_friend", methods={"POST"})
     */
    public function addFriendAction(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $friendId = $data['user-id'];

        /** @var User $user */
        $user = $this->getUser();

        /** @var User $friend */
        if ($friend = $this->userManager->getById($friendId)) {
            $friend->addFriend($user);
            $friend->addFriendWithMe($user);
            $user->addFriend($friend);
            $user->addFriendWithMe($friend);


            if ($this->userManager->save($friend) && $this->userManager->save($user)) {
                return new Response($this->serializeToJson($user, ['user_detail']), 200, [
                    'content-type' => self::JSON_CONTENT_TYPE,
                ]);
            }

            return new Response($this->serializeToJson([
                'error' => 'user.friend_or_user_not_saved'
            ], ['error-not-saved']), 500, [
                'content-type' => self::JSON_CONTENT_TYPE,
            ]);
        }

        return new Response($this->serializeToJson([
            'error' => 'user.friend_id_not_found',
        ], ['error-not-found']), 404, [
            'content-type' => self::JSON_CONTENT_TYPE
        ]);
    }

    /**
     * @OA\Post(
     *     operationId="registerDevice",
     *     summary="Register new device to authenticated user",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              title="RegisterDeviceObject",
     *              type="object",
     *              @OA\Property(property="deviceId", type="string", example="124324-4523423-34234123", nullable=false),
     *              @OA\Property(property="name", type="string", example="iPhone von Frederik", nullable=false),
     *              @OA\Property(property="os", type="string", example="iOS", nullable=true),
     *              @OA\Property(property="appVersion", type="string", example="1.0.2", nullable=true),
     *              @OA\Property(property="firebaseToken", type="string", example="Ada34§4d2dD_sfQ3dAcscsegr", nullable=false),
     *         )
     *     ),
     *     @OA\Response(response="200", description="Returns the registered device"),
     *     @OA\Response(response="401", description="Login faild. Invalid credentials")
     * )
     * @Route("/devices", name="devices_register", methods={"POST"})
     */
    public function registerDeviceAction(Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);
            $data['user'] = $this->getUser();

            if ($device = $this->deviceManager->create($data, true)) {
                return new Response($this->serializeToJson($device, ['user_device', 'user_list']), 200, [
                    'content-type' => self::JSON_CONTENT_TYPE,
                ]);
            }
        } catch (ValidationException $validationException) {
            return new JsonResponse([
                'error' => 'data.not_valid',
            ], 400);
        }
    }

    /**
     * @param File $file
     * @param UserInterface $user
     * @return Media
     */
    protected function saveImageMediaBundle(File $file, UserInterface $user): Media
    {
        $media = new Media();
        $media->setName($file->getFilename());
        $media->setContext('user_image');
        $media->setAuthorName($user->getUsername());
        $media->setProviderName('sonata.media.provider.image');
        $media->setBinaryContent($file);
        $this->mediaManager->save($media);

        $fs = new Filesystem();
        $fs->remove($file);

        return $media;
    }

    /**
     * @param string $base64
     * @return File|null
     * @throws \Exception
     */
    protected function base64ToJpeg(string $base64): ?File
    {
        if (empty($base64)) {
            return null;
        }

        $fs = new Filesystem();

        if (!is_dir($this->tempUserImagePath)) {
            $fs->mkdir($this->tempUserImagePath);
        }

        $bin = base64_decode($base64);

        // Load GD resource from binary data
        $im = imageCreateFromString($bin);

        // Make sure that the GD library was able to load the image
        // This is important, because you should not miss corrupted or unsupported images
        if (!$im) {
            throw new \Exception('base64 value is not a valid image');
        }

        // Specify the location where you want to save the image
        $img_file = $this->tempUserImagePath . DIRECTORY_SEPARATOR . md5(date('Y-m-d H:i:s')) . '.png';

        // Save the GD resource as PNG in the best possible quality (no compression)
        // This will strip any metadata or invalid contents (including, the PHP backdoor)
        // To block any possible exploits, consider increasing the compression level
        imagepng($im, $img_file, 0);

        return new File($img_file);
    }
}
