<?php

namespace App\tests\Unit;

use App\Entity\Mark;
use App\Entity\Recipe;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeTest extends KernelTestCase
{
    public function getEntity(): Recipe
    {
        return (new Recipe())->setName('recipe #1')
        ->setDesciption('Description #1')
        ->setIsFavorite(true)
        ->setCreatedat(new \DateTimeImmutable())
        ->setUpdatedat(new \DateTimeImmutable());

    }
    public function testEntityIsValide(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $recipe = $this->getEntity();

        $errors = $container->get('validator')->validate($recipe);

        $this->assertCount(0, $errors);
    }

    public function testInvalidName()
    {
        self::bootKernel();
        $container = static::getContainer();

        $recipe = $this->getEntity();
        $recipe->setName('');

        $errors = $container->get('validator')->validate($recipe);

        $this->assertCount(2, $errors);
    }

    public function testAverage()
    {
        $recipe = $this->getEntity();
        $user = static::getContainer()
            ->get('doctrine.orm.entity_manager')
            ->find(User::class, 1);

        for ($i=0; $i < 5; $i++) 
        { 
            $mark = new Mark();
            $mark->setMark(2)
                ->setUser($user)
                ->setRecipe($recipe);
            
            $recipe->addMark($mark);            
        }

        $this->assertTrue(2.0 === $recipe->getAverage());
    }
}
