<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/category/{slug}', name: 'product_category')]
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);
        if (!$category) {
            throw $this->createNotFoundException("La Catégorie demandée n'existe pas !");
        }
        return $this->render('product/public/category.html.twig', [
            'slug' => $slug,
            'category' => $category,
        ]);
    }
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
