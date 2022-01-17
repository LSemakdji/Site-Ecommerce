<?php

namespace App\Controller\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseConfirmationController extends AbstractController
{

    protected $cartService;
    protected $em;
    protected $persister;

    public function __construct(CartService $cartService, EntityManagerInterface $em, PurchasePersister $persister)
    {
        $this->cartService = $cartService;
        $this->em = $em;
        $this->persister = $persister;
    }

    #[Route('/purchase/confirm', name: 'purchase_confirm')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour passer une commande')]
    public function confirm(Request $request)
    {
        // 1. lire les données du formulaire
        $form = $this->createForm(CartConfirmationType::class);

        $form->handleRequest($request);

        // 2. si le formulaire n'a pas été soumis : degager 
        if (!$form->isSubmitted()) {
            $this->addFlash('warning', "Vous devez remplir le formulaire de confirmation");
            return $this->redirectToRoute('cart_show');
        }
        // 3. si je ne suis pas connecté : degager
        $user = $this->getUser();
        // 4. si il n'y a pas de produits dans mon panier : degager
        $cartItems = $this->cartService->getDetailedCartItems();

        if (count($cartItems) === 0) {
            $this->addFlash('warning', 'Vous ne pouvez confirmer un commande avec un panier vide');
            return $this->redirectToRoute('cart_show');
        }

        // 5. créer une Purchase
        /** @var Purchase */
        $purchase = $form->getData();

        // 6. Lier la Purchase à l'utilisateur connecté
        // 7. Lier la Purchase avec les produits qu sont dans le panier
        // 8. enregistrer la commande 
        $this->persister->storePurchase($purchase);

        // 9. on vide le panier après une commande


        return $this->redirectToRoute('purchase_payment_form', [
            'id' => $purchase->getId(),
        ]);
    }
}
