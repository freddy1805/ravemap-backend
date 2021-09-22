<?php

namespace App\Service\Entity;

use App\Entity\User;
use App\Util\EntityMapper;

class UserManager extends BaseManager {

    protected string $repoName = 'App:User';

    /**
     * @var array
     */
    protected array $validation = [
        'username',
        'email',
        'plainPassword',
    ];

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return User::class;
    }

    /**
     * @param string $username
     * @return bool
     */
    public function isUsernameAvailable(string $username): bool
    {
        return $this->repository->findOneBy(['username' => $username]) === null;
    }

    /**
     * @param $username
     * @return mixed
     */
    public function generateUniqueUsername($username){
        $userNamesList = array();
        $firstChar = str_split($username, 1)[0];
        $firstTwoChar = str_split($username, 2)[0];

        $numSufix = explode('-', date('Y-m-d-H'));

        array_push($userNamesList,
            $username,
            $firstChar.$username,
            $firstTwoChar.$username,
            $username.$numSufix[0],
            $username.$numSufix[1],
            $username.$numSufix[2],
            $username.$numSufix[3]
        );

        $isAvailable = false;
        $index = 0;
        $maxIndex = count($userNamesList) - 1;

        do {
            $availableUserName = $userNamesList[$index];
            $isAvailable = $this->isUsernameAvailable($availableUserName);
            $limit =  $index >= $maxIndex;
            $index += 1;
            if($limit){
                break;
            }
        } while (!$isAvailable);

        return $availableUserName;
    }

    /**
     * @param string $token
     * @return User|null
     */
    public function getByConfirmationToken(string $token): ?User
    {
        return $this->repository->findOneBy(['confirmationToken' => $token]);
    }
}
