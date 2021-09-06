<?php

namespace App\Controller\Main;

use App\Entity\Invite;
use App\Service\Entity\InviteManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class InviteController
 * @package App\Controller
 * @Route("/invite", name="ravemap_invite_")
 */
class InviteController extends AbstractController {

    private InviteManager $inviteManager;

    /**
     * InviteController constructor.
     * @param InviteManager $inviteManager
     */
    public function __construct(InviteManager $inviteManager)
    {
        $this->inviteManager = $inviteManager;
    }

    /**
     * @Route("/{id}", name="detail", methods={"GET"})
     */
    public function inviteAction(string $id): Response
    {
        if ($invite = $this->inviteManager->getById($id)) {

            if ($invite->getToUser()) {
                throw new NotFoundHttpException('Invite not found');
            }

            return $this->render('Controller/Invite/detail.html.twig', [
                'invite' => $invite,
                'background' => false
            ]);
        }

        throw new NotFoundHttpException('Invite not found');
    }

    /**
     * @Route("/{id}/accept", name="accept", methods={"GET"})
     */
    public function acceptInviteAction(string $id): Response
    {
        /** @var Invite $invite */
        if ($invite = $this->inviteManager->getById($id)) {

            if (!$this->getUser()) {
                return $this->redirectToRoute('fos_user_security_login');
            }

            if ($invite->getToUser()) {
                throw new NotFoundHttpException('Invite not found');
            }

            $invite->setToUser($this->getUser());
            $invite->setStatus(Invite::STATUS_INVITE_ACCEPTED);

            if ($this->inviteManager->save($invite)) {
                $this->addFlash('event_accepted', $invite->getEvent()->getName());
            }

            return $this->redirectToRoute('index');
        }

        throw new NotFoundHttpException('Invite not found');
    }

    /**
     * @Route("/{id}/deny", name="deny", methods={"GET"})
     */
    public function denyInviteAction(string $id): Response
    {
        /** @var Invite $invite */
        if ($invite = $this->inviteManager->getById($id)) {

            if (!$this->getUser()) {
                return $this->redirectToRoute('fos_user_security_login');
            }

            if ($invite->getToUser()) {
                throw new NotFoundHttpException('Invite not found');
            }

            $invite->setToUser($this->getUser());
            $invite->setStatus(Invite::STATUS_INVITE_DENIED);

            if ($this->inviteManager->save($invite)) {
                $this->addFlash('event_denied', $invite->getEvent()->getName());
            }
            return $this->redirectToRoute('index');
        }

        throw new NotFoundHttpException('Invite not found');
    }

    /**
     * @Route("/{id}/maybe", name="maybe", methods={"GET"})
     */
    public function maybeInviteAction(string $id): Response
    {
        /** @var Invite $invite */
        if ($invite = $this->inviteManager->getById($id)) {

            if (!$this->getUser()) {
                return $this->redirectToRoute('fos_user_security_login');
            }

            if ($invite->getToUser()) {
                throw new NotFoundHttpException('Invite not found');
            }

            $invite->setToUser($this->getUser());
            $invite->setStatus(Invite::STATUS_INVITE_MAYBE);

            if ($this->inviteManager->save($invite)) {
                $this->addFlash('event_maybe', $invite->getEvent()->getName());
            }
            return $this->redirectToRoute('index');
        }

        throw new NotFoundHttpException('Invite not found');
    }
}
