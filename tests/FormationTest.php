<?php

namespace App\Tests;

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

/**
 * Description of FormationTest
 * Classe de test pour la classe Formation.
 * @author Jadem
 */
class FormationTest extends TestCase
{
    // Teste la méthode getPublishedAtString().
    public function testGetPublishedAtString()
    {
        // Crée une nouvelle instance de la classe Formation
        $formation = new Formation();
        
        // Définit la date de publication au 30 décembre 2022
        $formation->setPublishedAt(new \DateTime("2022-12-30"));
        
        // Vérifie si la méthode getPublishedAtString() retourne la chaîne attendue
        $this->assertEquals("30/12/2022", $formation->getPublishedAtString());
    }
}
