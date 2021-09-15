<?php

namespace App\Service\Entity;

use App\Entity\Event;
use App\Entity\Invite;
use App\Entity\Post;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Message\EventRemovedMessage;
use App\Service\GeolocationService;
use App\Util\EntityMapper;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Sonata\MediaBundle\Provider\ImageProvider;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class EventManager extends BaseManager {

    protected string $repoName = 'App:Event';

    protected array $validation = [
        'name',
        'date',
        'description',
        'location',
        'approval',
        'eventMode',
        'maxInvites'
    ];

    private GeolocationService $geolocationService;

    private InviteManager $inviteManager;

    private ImageProvider $imageProvider;

    private CacheInterface $cache;

    private MessageBusInterface $messageBus;

    /**
     * EventManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param GeolocationService $geolocationService
     * @param InviteManager $inviteManager
     * @param ImageProvider $imageProvider
     * @param MessageBusInterface $messageBus
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        GeolocationService $geolocationService,
        InviteManager $inviteManager,
        ImageProvider $imageProvider,
        MessageBusInterface $messageBus
    ) {
        parent::__construct($entityManager);
        $this->geolocationService = $geolocationService;
        $this->inviteManager = $inviteManager;
        $this->imageProvider = $imageProvider;
        $this->messageBus = $messageBus;

        $cacheClient = RedisAdapter::createConnection('redis://localhost');
        $this->cache = new RedisAdapter($cacheClient);
    }

    /**
     * @param User $creator
     * @return Event[]|object[]
     */
    public function getByCreator(User $creator): array
    {
        return $this->repository->findBy(['creator' => $creator]);
    }

    /**
     * @return array
     */
    public function getMessageChannels(): array
    {
        $channels = [];
        $events = $this->repository->findAll();

        /** @var Event $event */
        foreach ($events as $event) {
            $channels[$event->getId()] = [
                'connections' => [],
                'posts' => $event->getPosts()
            ];
        }

        return $channels;
    }

    /**
     * @param UserInterface $user
     * @param Event $event
     * @param string $content
     * @return array
     */
    public function postNewMessage(UserInterface $user, Event $event, string $content): array
    {
        /** @var Post $post */
        $post = EntityMapper::arrayToEntity(Post::class, [
            'author' => $user,
            'event' => $event,
            'content' => $content
        ]);

        $event->addPost($post);
        $this->entityManager->persist($post);
        $this->save($event);

        return [
            'new' => $post,
            'messages' => $this->makeArrayMessages($event->getPosts()->toArray())
        ];
    }

    /**
     * @param string $id
     * @return array
     */
    public function getArrayMessages(string $id): array
    {
        /** @var Event $event */
        $event = $this->getById($id);

        return $this->makeArrayMessages($event->getPosts()->toArray());
    }

    /**
     * @param Post[] $messages
     * @return array
     * @throws InvalidArgumentException
     */
    public function makeArrayMessages(array $messages): array
    {
        $this->cache->clear();
        $result = [];
        /**
         * @var int $key
         * @var Post $message
         */
        foreach ($messages as $key => $message) {
            $result[$key] = $this->cache->get('message-' . $message->getId(), function (ItemInterface $cacheItem) use ($message) {
                $cacheItem->expiresAfter(86400);
                $arrayMessage = $message->toArray();
                if ($image = $message->getAuthor()->getImage()) {
                    $arrayMessage['author']['image']['medium'] = 'https://ravemap.tk' . $this->imageProvider->generatePublicUrl(
                        $image,
                        'user_image_medium'
                    );
                }
                return $arrayMessage;
            });
        }

        return $result;
    }

    /**
     * @param string $eventId
     * @param array $data
     * @return Invite|object|null
     * @throws ValidationException
     */
    public function createInvite(string $eventId, array $data): ?object
    {
        // TODO: EventDispatcher -> NotifyUsers

        $data['event'] = $this->getById($eventId);

        return $this->inviteManager->create($data, true);
    }

    /**
     * @param Event|object $object
     * @return boolean
     */
    public function save(object $object): bool
    {
        try {
            $this->updateLocation($object);
            $this->entityManager->persist($object);
            $this->entityManager->flush();
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @param Event|object $object
     * @return bool
     */
    public function remove(object $object): bool
    {
        $eventId = $object->getId();

        try {
            foreach ($object->getInvites() as $invite) {
                $this->entityManager->remove($invite);
            }

            foreach ($object->getPosts() as $post) {
                $this->entityManager->remove($post);
            }

            $this->entityManager->remove($object);
            $this->entityManager->flush();

            $this->messageBus->dispatch(new EventRemovedMessage($object, $eventId));
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param Event $event
     * @return Event
     */
    public function updateLocation(Event &$event): Event
    {
        $location = $event->getLocation();

        if ($administrativeLevel = $this->geolocationService->getAdministrativeLevel($location)) {
            $adminLevelArr = explode(',', $administrativeLevel);
            $location['name'] = trim($administrativeLevel);
            $location['city'] = trim($adminLevelArr[0]);
            $location['country'] = trim($adminLevelArr[1]);
        }

        $event->setLocation($location);

        return $event;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return Event::class;
    }
}
