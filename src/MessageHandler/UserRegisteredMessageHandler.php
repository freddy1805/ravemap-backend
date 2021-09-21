<?php

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\UserRegisteredMessage;
use App\Service\Entity\UserManager;
use FOS\UserBundle\Mailer\TwigSwiftMailer;
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
     * @var TwigSwiftMailer
     */
    private TwigSwiftMailer $mailer;

    /**
     * UserRegisteredMessageHandler constructor.
     * @param UserManager $userManager
     * @param TwigSwiftMailer $mailer
     */
    public function __construct(UserManager $userManager, TwigSwiftMailer $mailer)
    {
        $this->userManager = $userManager;
        $this->mailer = $mailer;
    }

    /**
     * @param UserRegisteredMessage $userRegisteredMessage
     */
    public function __invoke(UserRegisteredMessage $userRegisteredMessage)
    {
        /** @var User $user */
        $user = $userRegisteredMessage->getUser();

        $this->mailer->sendConfirmationEmailMessage($user);
    }
}
