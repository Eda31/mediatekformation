<?php

namespace App\Tests\Validations;

use App\Entity\Formation;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Description of FormationValidationsTest
 * Classe de test pour les validations de l'entité Formation.
 * @author Jadem
 */
class FormationValidationsTest extends KernelTestCase
{
    /**
     * Retourne une instance de Formation préconfigurée pour les tests
     * @return Formation
     */
    public function getFormation(): Formation
    {
        return (new Formation())
                ->setTitle("Cours")
                ->setVideoId("")
                ->setPublishedAt(new DateTime('2026-11-26'));
    }
    
    /**
     * Vérifie le nombre d'erreurs de validation pour une formation donnée
     * @param Formation $formation
     * @param int $nbErreursAttendues
     * @param string $message
     */
    public function assertErrors(Formation $formation, int $nbErreursAttendues, string $message="")
    {
        // Initialise le kernel de Symfony
        self::bootKernel();
        // Récupère le service de validation
        $validator = self::getContainer()->get(ValidatorInterface::class);
        // Valide la formation et récupère les erreurs
        $errors = $validator->validate($formation);
        
        // Débogage : affiche toutes les erreurs détectées
        foreach ($errors as $error) {
            echo $error->getPropertyPath() . ': ' . $error->getMessage() . PHP_EOL;
        }
        
        // Vérifie le nombre d'erreurs
        $this->assertCount($nbErreursAttendues, $errors, $message);
    }
    
    /**
     * Teste si la date de publication ne peut pas être dans le futur
     */
    public function testPublishedAtNotBeInTheFuture()
    {
        // Récupère une instance de Formation configurée pour le test
        $formation = $this->getFormation();
        // Vérifie le nombre d'erreurs de validation pour la date de publication dans le futur
        $this->assertErrors($formation, 1, "La date ne peut pas être postérieure à aujourd'hui.");
    }
    
}
