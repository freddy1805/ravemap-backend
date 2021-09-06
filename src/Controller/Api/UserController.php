<?php

namespace App\Controller\Api;

use App\Entity\Media;
use App\Entity\User;
use App\Service\Entity\EventManager;
use App\Service\Entity\UserManager;
use Sonata\MediaBundle\Entity\MediaManager;
use Sonata\MediaBundle\Form\Type\ApiMediaType;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
class UserController extends BaseApiController {

    private UserManager $userManager;

    private EventManager $eventManager;

    private $mediaManager;

    private FormFactoryInterface $formFactory;

    private Pool $mediaPool;

    /**
     * UserController constructor.
     * @param ContainerInterface $container
     * @param UserManager $userManager
     * @param EventManager $eventManager
     * @param FormFactoryInterface $factory
     * @param Pool $mediaPool
     */
    public function __construct(
        ContainerInterface $container,
        UserManager $userManager,
        EventManager $eventManager,
        FormFactoryInterface $factory,
        Pool $mediaPool
    ) {
        parent::__construct($container);
        $this->userManager = $userManager;
        $this->eventManager = $eventManager;
        $this->formFactory = $factory;
        $this->mediaManager = $container->get('sonata.media.manager.media');
        $this->mediaPool = $mediaPool;
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
     *     @OA\Response(response="200", description="Returns the updated user"),
     *     @OA\Response(response="401", description="Login faild. Invalid credentials")
     * )
     * @Route("/me/image", name="update_image", methods={"POST"})
     */
    public function updateImageAction(Request $request): Response
    {
        try {
            $file = null;
            /** @var UploadedFile $uploadedFile */
            foreach ($request->files as $uploadedFile) {
                $file = $uploadedFile;
            }

            if (!$file) {
                throw new NotFoundHttpException('binaryContent not found');
            }

            $user = $this->getUser();
            $media = $this->saveImageMediaBundle($file, $user);


            $this->userManager->update($user, [
                'image' => $media
            ], true);

            return new Response($this->serializeToJson($user, ['user_detail']), 200, [
                'content-type' => self::JSON_CONTENT_TYPE
            ]);
        } catch (\RuntimeException | NotFoundHttpException | \InvalidArgumentException $ex ) {
            return new Response($this->serializeToJson(['error' => 'Coud not upload image'], ['upload_error']), 400, [
                'content-type' => self::JSON_CONTENT_TYPE
            ]);
        }
    }

    /**
     * @param UploadedFile $file
     * @param UserInterface $user
     * @return Media
     */
    protected function saveImageMediaBundle(UploadedFile $file, UserInterface $user): Media
    {
        $media = new Media();
        $media->setName($file->getClientOriginalName());
        $media->setContext('user_image');
        $media->setAuthorName($user->getUsername());
        $media->setProviderName('sonata.media.provider.image');
        $media->setBinaryContent($file);
        $this->mediaManager->save($media);

        return $media;
    }
}
