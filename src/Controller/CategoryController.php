<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'category_show')]
    public function show(CategoryRepository $categoryRepository, ProductRepository $productRepository)
    {
        $category = $categoryRepository->findall();

        return $this->render('category/public/show.html.twig', [
            'category' => $category,
        ]);
    }
    #[Route('/category/{slug}', name: 'category_product_show')]
    public function category($slug, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);
        if (!$category) {
            $this->addFlash('warning', "La catégorie n'existe pas");
            return $this->redirectToRoute('category_show');
            //throw $this->createNotFoundException("La Catégorie demandée n'existe pas !");
        }
        return $this->render('category/public/category_product.html.twig', [
            'slug' => $slug,
            'category' => $category,
        ]);
    }
}
