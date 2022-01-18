<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Bezhanov\Faker\Provider\Commerce;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $encoder;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr-FR');
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        $admin = new User();

        $hash = $this->encoder->hashPassword($admin, "password");

        $admin->setEmail("admin@gmail.com")
            ->setFirstName("lou")
            ->setName("sem")
            ->setFullName("lou " . "sem")
            ->setPassword($hash)
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $users = [];

        for ($u = 0; $u < 5; $u++) {
            $user = new User();
            $hash = $this->encoder->hashPassword($user, "password");
            $user->setEmail("user$u@gmail.com")
                ->setName($faker->lastName())
                ->setFirstName($faker->firstName())
                ->setFullName($user->getName() . " " . $user->getFirstName())
                ->setPassword($hash);
            $users[] = $user;
            $manager->persist($user);
        }
        $products = [];
        for ($c = 0; $c < 7; $c++) {
            $category = new Category;
            $category->setName($faker->department());
            $manager->persist($category);

            for ($p = 0; $p < mt_rand(10, 20); $p++) {
                $product = new Product;
                $product->setName($faker->productName())
                    ->setShortDescription($faker->realText())
                    ->setMainPicture($faker->imageUrl(400, 400, true))
                    ->setPrice(mt_rand(4000, 20000))
                    ->setCategory($category);

                $products[] = $product;

                $manager->persist($product);
            }
        }
        for ($p = 0; $p < mt_rand(20, 40); $p++) {
            $purchase = new Purchase;
            $purchase->setFullName($faker->name)
                ->setAddress($faker->streetAddress())
                ->setPostalCode($faker->postcode())
                ->setCity($faker->city())
                ->setUser($faker->randomElement($users))
                ->setTotal(mt_rand(2000, 30000))
                ->setPurchasedAt($faker->dateTimeBetween('-6 months'));

            $selectedProducts = $faker->randomElements($products, mt_rand(3, 5));
            foreach ($selectedProducts as $product) {
                $purchaseItem = new PurchaseItem;
                $purchaseItem->setProduct($product)
                    ->setQuantity(mt_rand(1, 3))
                    ->setProductName($product->getName())
                    ->setProductPrice($product->getPrice())
                    ->setTotal(
                        $purchaseItem->getProductPrice() * $purchaseItem->getQuantity()
                    )
                    ->setPurchase($purchase);
                $manager->persist($purchaseItem);
            }
            if ($faker->boolean(80)) {
                $purchase->setStatus(Purchase::STATUS_PAID);
            }
            $manager->persist($purchase);
        }
        $manager->flush();
    }
}
