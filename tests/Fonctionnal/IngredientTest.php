<?php

namespace App\Tests\Fonctionnal;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IngredientTest extends WebTestCase
{
    public function testIfCreateIngredientIsSuccessFull(): void
    {
        $client = static::createClient();

        //recup url generator
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");
        //recup entity manager
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);

        $client->loginUser($user);

        //se rendre sur la page de creation d'un ingredient

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredient.new'));

        //gerer le formulaire
        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => 'un ingredient',
            'ingredient[price]' => floatval(33)
        ]);

        $client->submit($form);

        //gerer la redirection

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        
        $client->followRedirect();

        //gerer l'alerte box et la route

        $this->assertSelectorTextContains('div.alert-success', 'Votre ingredient à été crée avec succés!');

        $this->assertRouteSame('ingredient.index');


    }

    public function testIfLIstingIngredientIsSuccessful (): void
    {
        $client = static::createClient();

        //recup url generator
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");
        //recup entity manager
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);

        $client->loginUser($user);
        
        $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredient.index'));

        $this->assertResponseIsSuccessful();

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        
        $this->assertRouteSame('ingredient.index');
    }

    public function testIfUpdateAnIngredientIsSuccessful (): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);

        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('ingredient.edit', ['id' => $ingredient->getId()])
        );

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => 'un ingredient',
            'ingredient[price]' => floatval(34)
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'Votre ingredient à été modifié avec succés!');

        $this->assertRouteSame('ingredient.index');
    }

    public function testDeleteAnIngredientIsSUccessfull (): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);

        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('ingredient.edit', ['id' => $ingredient->getId()])
        );

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => 'un ingredient',
            'ingredient[price]' => floatval(34)
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'Votre ingredient à été supprimé avec succés!');

        $this->assertRouteSame('ingredient.delete');
    }
}
