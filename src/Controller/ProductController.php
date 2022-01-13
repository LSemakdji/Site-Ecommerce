<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/category/{category_slug}/{slug}', name: 'product_show')]
    public function show($slug, ProductRepository $productRepository)
    {
        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);
        if (!$product) {
            throw $this->createNotFoundException("Le produit demandé n'existe pas !");
        }
        return $this->render('product/public/show.html.twig', [
            'product' => $product,
        ]);
    }
}
