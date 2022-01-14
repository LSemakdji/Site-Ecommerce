<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasesListController extends AbstractController
{



    #[Route('/purchases', name: 'purchase_index')]
    #[IsGranted("ROLE_USER", message: 'Vous devez être connecté pour accéder à vos commandes')]
    public function index()
    {
        // 1. je dois m'assurer que la personne est connectée (sinon => page d'accueil)->Security
        /** @var User */
        $user = $this->getUser();
        // 2.Savoir QUI est connecté->Security
        // 3. on veut passer l'utilisateur connecté a Twig afin d'afficher ses commandes Environment de Twig/Response
        return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases(),
        ]);
    }
}
