<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use App\Entity\Category;
use Bezhanov\Faker\Provider\Commerce;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr-FR');
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        for ($c = 0; $c < 7; $c++) {
            $category = new Category;
            $category->setName($faker->department())
                ->setSlug(strtolower($this->slugger->slug($category->getName())));
            $manager->persist($category);

            for ($p = 0; $p < mt_rand(10, 20); $p++) {
                $product = new Product;
                $product->setName($faker->productName())
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture($faker->imageUrl(400, 400, true))
                    ->setPrice(mt_rand(4000, 20000))
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category);
                $manager->persist($product);
            }
        }
        $manager->flush();
    }
}
