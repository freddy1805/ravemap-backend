<?php

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\UserRegisteredMessage;
use App\Service\Entity\UserManager;
use FOS\UserBundle\Mailer\Mailer;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Class UserRegisteredMessageHandler
 * @package App\MessageHandler
 * @author Frederik Roettgerkamp <frederik@roettgerkamp.com>
 */
class UserRegisteredMessageHandler implements MessageHandlerInterface
{
    /**
     * @var UserManager
     */
    private UserManager $userManager;

    /**
     * @var TokenGeneratorInterface
     */
    private TokenGeneratorInterface $tokenGenerator;

    /**
     * @var Mailer
     */
    private Mailer $mailer;

    /**
     * UserRegisteredMessageHandler constructor.
     * @param UserManager $userManager
     * @param TokenGeneratorInterface $tokenGenerator
     * @param Mailer $mailer
     */
    public function __construct(
        UserManager $userManager,
        TokenGeneratorInterface $tokenGenerator,
        Mailer $mailer
    ) {
        $this->userManager = $userManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
    }

    /**
     * @param UserRegisteredMessage $userRegisteredMessage
     */
    public function __invoke(UserRegisteredMessage $userRegisteredMessage)
    {
        /** @var User $user */
        $user = $userRegisteredMessage->getUser();

        $user->setEnabled(false);
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }

        $this->mailer->sendConfirmationEmailMessage($user);
    }
}
