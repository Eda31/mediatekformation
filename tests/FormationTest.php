<?php

namespace App\Tests;

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

/**
 * Description of FormationTest
 * Classe de test pour la classe Formation
 * @author Jadem
 */
class FormationTest extends TestCase
{
    public function testGetPublishedAtString()
    {
        // Crée une nouvelle instance de la classe Formation
        $formation = new Formation();
        
        // Définit la date de publication au 30 décembre 2025
        $formation->setPublishedAt(new \DateTime("2025-12-30"));
        
        // Vérifie si la méthode getPublishedAtString() retourne la chaîne attendue
        $this->assertEquals("30/12/2025", $formation->getPublishedAtString());
    }
}
