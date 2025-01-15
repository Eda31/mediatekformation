<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of PlaylistsControllerTest
 * Classe de test pour le PlaylistsController
 * @author Jadem
 */
class PlaylistsControllerTest extends WebTestCase
{
    /**
     * Teste l'accès à la page des formations.
     */
    public function testAccesPage()
    {
        $client = static::createClient();
        $client->request('GET', '/playlists');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    
    /**
     * Teste le contenu de la page.
     */
    public function testContenuPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists');
        
        $this->assertSelectorTextContains('th', 'playlist');
        $this->assertCount(4, $crawler->filter('th'));
        $this->assertSelectorTextContains('h5', 'Bases de la programmation (C#)');
    }
    
    /**
     * Teste le lien vers une formation.
     */
    public function testLinkFormation()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists');
        
        $link = $crawler->filter('a')->eq(9)->attr('href');
        $crawler = $client->request('GET', $link);
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertEquals('/playlists/playlist/17', $uri);
    }
    
    /**
     * Teste le filtre des formations.
     */
    public function testFiltreFormation()
    {
        $client = static::createClient();
        $client->request('GET', '/playlists');
        
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'Bases de la programmation (C#)'
        ]);
        $this->assertCount(1, $crawler->filter('h5'));
        $this->assertSelectorTextContains('h5', 'Bases de la programmation (C#)');
    }
}
