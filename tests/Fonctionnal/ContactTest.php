<?php

namespace App\Tests\fonctionnal;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Formulaire de contact');

        //récuperer le formulaire
        $submitButton = $crawler->selectButton('Soumettre ma demande');
        $form = $submitButton->form();

        $form["contact[fullName]"] = "jean Dupont";
        $form["contact[email]"] = "jean.dupont@gmail.com";
        $form["contact[subjet]"] = "test";
        $form["contact[message]"] = "test";

        //soumettre le formulaire
        $client->submit($form);

        //vérifier le HTTP
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        //vérifier l'envoi de mail
        $this->assertEmailCount(1);

        $client->followRedirect();

        // verifier la presence du message de succes

        $this->assertSelectorTextContains(
            'div.alert.alert.success.mt-4',
            'Votre demande a été envoyé avec succès!'
        );

    }
}
