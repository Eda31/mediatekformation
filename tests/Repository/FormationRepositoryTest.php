<?php

namespace App\Tests\Repository;

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Repository\FormationRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Description of FormationRepositoryTest
 * Classe de test pour le FormationRepository
 * @author Jadem
 */
class FormationRepositoryTest extends KernelTestCase
{
    /**
     * Récupère le repository de Formation
     * @return FormationRepository
     */
    public function recupRepository(): FormationRepository
    {
        self::bootKernel();
        $repository = self::getContainer()->get(FormationRepository::class);
        return $repository;
    }
    
    /**
     * Teste le nombre de formations.
     */
    public function testNbFormations()
    {
        $repository = $this->recupRepository();
        $nbFormations = $repository->count([]);
        $this->assertEquals(237, $nbFormations);
    }
    
    /**
     * Création d'une instance de Formation avec le titre, la video et la date de publication
     * @return Formation
     */
    public function newFormation(): Formation
    {
        $formation = (new Formation())
                ->setTitle("Cours")
                ->setVideoId("")
                ->setPublishedAt(new DateTime("now"));
        return $formation;
    }
    
    /**
     * Teste l'ajout d'une formation.
     */
    public function testAddFormation()
    {
        $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $nbFormations = $repository->count([]);
        $repository->add($formation, true);
        $this->assertEquals($nbFormations + 1, $repository->count([]), "erreur lors de l'ajout");
    }
    
    /**
     * Teste la suppression d'une formation.
     */
    public function testRemoveFormation()
    {
        $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $repository->add($formation, true);
        $nbFormations = $repository->count([]);
        $repository->remove($formation, true);
        $this->assertEquals($nbFormations - 1, $repository->count([]), "erreur lors de la suppression");
    }
    
    /**
     * Teste la méthode findByContainValue.
     */
    public function testFindByContainValue()
    {
        $repository = $this->recupRepository();

        $formation1 = $this->newFormation();
        $formation1->setTitle("PHP Formation");
        $repository->add($formation1, true);

        $formation2 = $this->newFormation();
        $formation2->setTitle("Symfony Formation");
        $repository->add($formation2, true);

        // Test avec une valeur existante
        $resultWithExistingValue = $repository->findByContainValue('title', 'PHP');
        $this->assertCount(1, $resultWithExistingValue);
        $this->assertEquals($formation1->getTitle(), $resultWithExistingValue[0]->getTitle());

        // Test avec une valeur inexistante
        $resultWithNonExistingValue = $repository->findByContainValue('title', 'Personne');
        $this->assertCount(0, $resultWithNonExistingValue);

        // Test avec une valeur vide
        $resultWithEmptyValue = $repository->findByContainValue('title', '');
        $this->assertCount(239, $resultWithEmptyValue);
    }
    
    /**
     * Teste la méthode findAllForOnePlaylist.
     */
    public function testFindAllForOnePlaylist()
    {
        $repository = $this->recupRepository();
        
        $playlist = new Playlist();
        $playlist->setName("Ma playlist");
        
        $formation1 = $this->newFormation();
        $formation1->setPlaylist($playlist);
        $repository->add($formation1, true);
        
        $formation2 = $this->newFormation();
        $formation2->setPlaylist($playlist);
        $repository->add($formation2, true);
        
        $resultWithExistingPlaylist = $repository->findAllForOnePlaylist($playlist);
        $this->assertCount(2, $resultWithExistingPlaylist);
        
        $resultWithNonExistingPlaylist = $repository->findAllForOnePlaylist(99);
        $this->assertCount(0, $resultWithNonExistingPlaylist);
    }
}
