<?php

namespace App\EventDispatcher;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Event\PurchaseSuccessEvent;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;
    protected $mailer;
    protected $security;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer, Security $security)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'sendSuccessEmail'
        ];
    }
    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        // 1. Recuperer le user actuellement en ligne (pour connaitre son adresse)
        // Security
        /** @var User */
        $currentUser = $this->security->getUser();
        // 2. Recuperer la commande(je la trouverais dans PurchaseSuccessEvent)
        $purchase = $purchaseSuccessEvent->getPurchase();
        // 3. Écrire le mail(nouveau TemplatedEmail)
        $email = new TemplatedEmail();
        $email->to(new Address($currentUser->getEmail(), $currentUser->getFullName()))
            ->from("contact@mail.com")
            ->subject("Bravo, votre commande ({$purchase->getId()} a bien été confirmée .)")
            ->htmlTemplate('emails/purchase_success.html.twig')
            ->context([
                'purchase' => $purchase,
                'user' => $currentUser
            ]);

        // 1. recuperer le user actuellement en ligne 
        // 5. Envoyer l'email 
        $this->mailer->send($email);
        // MailerInterface

        $this->logger->info("Email envoyé pour la commande n°" . $purchaseSuccessEvent->getPurchase()->getId());
    }
}
