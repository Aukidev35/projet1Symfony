<?php

namespace App\tests\Fonctionnal\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactTest extends WebTestCase
{
    public function testCrudIsHere(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->getRepository(User::class)->findByOne(['id'=>1]);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, '/admin');

        $this->assertResponseIsSuccessful();

        $crawler = $client->clickLink('Demandes de contact');

        $client->click($crawler->filter('.action-new')->link());

        $this->assertResponseIsSuccessful();

        $client->request(Request::METHOD_GET, '/admin');

        $client->click($crawler->filter('.action-edit')->link());

        $this->assertResponseIsSuccessful();
    }
}