<?php

namespace App\Service\Entity;

use App\Entity\Device;

class DeviceManager extends BaseManager {

    protected string $repoName = 'App:Device';

    protected array $validation = [
        'user',
        'deviceId',
        'name',
        'firebaseToken'
    ];

    /**
     * @param string $token
     * @return Device|null
     */
    public function getByFirebaseToken(string $token): Device
    {
       return $this->repository->findOneBy(['firebaseToken' => $token]) ?? new Device();
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return Device::class;
    }
}
