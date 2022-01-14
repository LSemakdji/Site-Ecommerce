<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    protected $productRepository;
    protected $cartService;
    public function __construct(ProductRepository $productRepository, CartService $cartService)
    {
        $this->productRepository = $productRepository;
        $this->cartService = $cartService;
    }
    #[Route('/cart', name: 'cart_show')]
    public function show()
    {
        $form = $this->createForm(CartConfirmationType::class);
        $detailedCart = $this->cartService->getDetailedCartItems();
        $total = $this->cartService->getTotal();

        return $this->render('cart/index.html.twig', [
            'confirmationForm' => $form->createView(),
            'items' => $detailedCart,
            'total' => $total,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add', requirements: ["id" => "\d+"])]
    public function add($id, Request $request)
    {
        // 0. securisation : est-ce que le produit existe?
        $product = $this->productRepository->find($id);
        if (!$product) {
            $this->addFlash('warning', "Le produit n'existe pas");
            return $this->redirectToRoute('homepage');
        }

        $this->cartService->add($id);
        //gerer mes messages 
        //$this->addFlash('success', "Le produit à été ajouté au panier");

        if ($request->query->get('returnToCart')) {
            return $this->redirectToRoute('cart_show');
        }

        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }

    #[Route('/cart/decrement/{id}', name: 'cart_decrement', requirements: ["id" => "\d+"])]
    public function decrement($id)
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être décrementé ");
        }
        $this->cartService->decrement($id);
        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/delete/{id}', name: 'cart_delete', requirements: ["id" => "\d+"])]
    public function delete($id)
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être supprimé ");
        }
        $this->cartService->remove($id);
        return $this->redirectToRoute('cart_show');
    }
}
