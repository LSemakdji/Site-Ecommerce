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
}
