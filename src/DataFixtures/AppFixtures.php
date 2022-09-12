<?php

namespace App\DataFixtures;
use Faker\Factory;
use App\Entity\Mark;
// use Symfony\Flex\Recipe;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Recipe;
use App\Entity\Contact;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use phpDocumentor\Reflection\DocBlock\Tags\Generic;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {

        //user
        $users = [];
        $admin = new User();
        $admin->setFullName('Administrateur de mon projet')
            ->setPseudo('null')
            ->setEmail('admin@monprojet.fr')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ->setPlainPassword(('password'));
        
        $users[] = $admin;
        $manager->persist($admin);


        for ($i=0; $i < 10; $i++) { 
            $user = new User();
            $user->setFullName($this->faker->name())
                ->setPseudo($this->faker->firstName())
                ->setEmail($this->faker->email())
                ->setRoles(['Roles_USERS'])             
                ->setPlainPassword('password');

            $users[]= $user;    
            $manager->persist($user);
        }
        //ingredient

        $ingredients = [];

        for ($i=0; $i<=50; $i++)
        {
            $ingredient = new ingredient();
            $ingredient ->setName($this->faker->word())
                        ->setPrice(mt_rand(0,100))
                        ->setUser($users[mt_rand(0, count($users) - 1)])
                    ;
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);                      
        }

        //recipes
        $recipes = [];
        for ($j=0; $j<=25; $j++)
        {
            $recipe = new Recipe();
            $recipe->setName($this->faker->word())
                ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null)
                ->setnbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
                ->setDesciption($this->faker->text(300))
                ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null)
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false)
                ->setIsPublic(mt_rand(0, 1) == 1 ? true : false)
                ->setUser($users[mt_rand(0, count($users) - 1)]);
            
                for ($k=0; $k < mt_rand(5, 15); $k++)
                {
                    $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
                }
            $recipes[] = $recipe;
            $manager->persist($recipe);
        }
        
        //mark
        foreach ($recipes as $recipe) 
        {
            for ($i=0; $i < mt_rand(0, 4) ; $i++) { 
                $mark = new Mark();
                $mark->setMark(mt_rand(1, 5))
                    ->setUser($users[mt_rand(0, count($users) - 1)])
                    ->setRecipe($recipe);

                    $manager->persist($mark);
            }
        }

        // contact
        for ($i=0; $i < 5; $i++) { 
            $contact = new Contact();
            $contact->setFullName($this->faker->name())
                    ->setEmail($this->faker->email())
                    ->setSubjet('demande numéro ' . ($i+1))
                    ->setMessage($this->faker->text());
            
            $manager->persist($contact);
        }

        
        $manager->flush();
    }
}
