<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of FormationsControllerTest
 * Classe de test pour le FormationsController
 * @author Jadem
 */
class FormationsControllerTest extends WebTestCase
{
    /**
     * Teste l'accès à la page des formations.
     */
    public function testAccesPage()
    {
        $client = static::createClient();
        $client->request('GET', '/formations');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    
    /**
     * Teste le contenu de la page des formations.
     */
    public function testContenuPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations');
        
        $this->assertSelectorTextContains('th', 'formation');
        $this->assertCount(5, $crawler->filter('th'));
        $this->assertSelectorTextContains('h5', 'Eclipse n°8 : Déploiement');
    }
    
    /**
     * Teste le lien vers une formation depuis la page des formations.
     */
    public function testLinkFormation()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations');
        // Assurez-vous que l'index est correct
        $link = $crawler->filter('a')->eq(236)->attr('href');
        $crawler = $client->request('GET', $link);
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        // Vérifiez que l'URL est correcte
        $this->assertEquals('/formations/formation/231', $uri);
    }
    
    /**
     * Teste le filtre des formations depuis la page des formations.
     */
    public function testFiltreFormation()
    {
        $client = static::createClient();
        $client->request('GET', '/formations');
        
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'Eclipse n°7 : Tests unitaires'
        ]);
        $this->assertCount(1, $crawler->filter('h5'));
        $this->assertSelectorTextContains('h5', 'Eclipse n°7 : Tests unitaires');
    }
}
