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
     * @return string
     */
    public function getEntityClass(): string
    {
        return Device::class;
    }
}
