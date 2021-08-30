<?php

namespace App\Service\Entity;

use App\Entity\Event;
use App\Entity\Invite;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Service\GeolocationService;
use Doctrine\ORM\EntityManagerInterface;

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

    /**
     * EventManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param GeolocationService $geolocationService
     * @param InviteManager $inviteManager
     */
    public function __construct(EntityManagerInterface $entityManager, GeolocationService $geolocationService, InviteManager $inviteManager)
    {
        parent::__construct($entityManager);
        $this->geolocationService = $geolocationService;
        $this->inviteManager = $inviteManager;
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
