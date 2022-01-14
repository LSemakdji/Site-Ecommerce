<?php

namespace App\Cart;

use App\Cart\CartItem;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class CartService
{
    protected $session;
    protected $productRepository;

    //depuis symfony 6 on ne peut plus recuperer la sessionInterface dans le constructor, on passe alos par requestStack pour la recuperer
    public function __construct(RequestStack $session, ProductRepository $productRepository)
    {
        $this->session = $session->getSession();
        $this->productRepository = $productRepository;
    }
    protected function getCart()
    {
        return $this->session->get('cart', []);
    }
    protected function saveCart($cart)
    {
        return $this->session->set('cart', $cart);
    }

    public function empty()
    {
        $this->saveCart([]);
    }
    public function add($id)
    {
        // je recupère la session


        // 1. Retrouver le panier dans la session (sous forme de tableau)
        // 2. Si il n'existe pas encore, alors prendre un tableau vide
        $cart = $this->getCart();
        //[12 => 3 , 29 => 2]
        // 3. Voir si le produit ($id) existe deja dans le tableau
        // 4. Si c'est le cas, simplement augmenter la quantité
        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
            // 5. Sinon, ajouter le produit avec la quantité 1
        }
        $cart[$id]++;
        // 6.Enregistrer le tableau mis à jour dans la session
        $this->saveCart($cart);
    }
    public function remove(int $id)
    {
        $cart = $this->getCart();
        unset($cart[$id]);
        $this->saveCart($cart);
    }
    public function getTotal()
    {
        $total = 0;
        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);
            if (!$product) {
                continue;
            }
            $total += ($product->getPrice() * $qty);
        }
        return $total;
    }
    /**
     * Undocumented function
     *
     * @return CartItem[]
     */
    public function getDetailedCartItems(): array
    {
        $detailedCart = [];
        $total = 0;

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);
            if (!$product) {
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }
        return $detailedCart;
    }

    public function decrement(int $id)
    {
        $cart = $this->getCart();
        //si il existe une ligne dans le tableau qui a cette id on continue
        if (!array_key_exists($id, $cart)) {
            return;
        }
        //soit le produit est à 1 alors il faut simplement le supprimer

        if ($cart[$id] === 1) {
            $this->remove($id);
        } else {
            //soit le produit est à plus de 1, alors il faut le décrementer
            $cart[$id]--;
            $this->saveCart($cart);
        }
    }
}
