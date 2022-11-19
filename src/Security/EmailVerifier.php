<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier
{
    private $verifyEmailHelper;
    private $mailer;
    private $entityManager;

    public function __construct(private VerifyEmailHelperInterface $helper, private MailerInterface $mailerInterface, private EntityManagerInterface $em) {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailerInterface;
        $this->entityManager = $em;
    }

    public function sendEmailConfirmation(string $verifyEmailRouteName, UserInterface $userInterface, TemplatedEmail $email): void
    {
        // TODO Faire la confirmation de création de compte par email
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            $userInterface->getId(),
            $userInterface->getEmail(),
            ['id' => $userInterface->getId()]
        );

        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $email->context($context);
        // By default, mail messages are being sent to table MessengerMessages
        // https://symfony.com/doc/current/messenger.html
        // https://symfony.com/doc/current/mailer.html#creating-sending-messages
        // To receive the messages in users inbox, deactivate in messenger.yaml the line below
        // Symfony\Component\Mailer\Messenger\SendEmailMessage: async

        $this->mailer->send($email);
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request, UserInterface $userInterface): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $userInterface->getId(), $userInterface->getEmail());

        $userInterface->setIsVerified(true);

        $this->entityManager->persist($userInterface);
        $this->entityManager->flush();
    }
}
