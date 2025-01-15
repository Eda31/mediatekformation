<?php

namespace App\Tests\Repository;

use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Description of PlaylistRepositoryTest
 * Classe de test pour le PlaylistRepository.
 * @author Jadem
 */
class PlaylistRepositoryTest extends KernelTestCase
{
    /**
     * Récupère le repository de Playlist
     * @return PlaylistRepository
     */
    public function recupRepository(): PlaylistRepository
    {
        self::bootKernel();
        $repository = self::getContainer()->get(PlaylistRepository::class);
        return $repository;
    }
    
    /**
     * Teste le nombre de playlists.
     */
    public function testNbFormations()
    {
        $repository = $this->recupRepository();
        $nbPlaylists = $repository->count([]);
        $this->assertEquals(28, $nbPlaylists);
    }
    
    /**
     * Création d'une instance de Playlist avec le titre
     * @return Playlist
     */
    public function newPlaylist(): Playlist
    {
        $playlist = (new Playlist())
                ->setName('Symphony');
        return $playlist;
    }
    
    /**
     * Teste l'ajout d'une playlist.
     */
    public function testAddPlaylist()
    {
        $repository = $this->recupRepository();
        $playlist = $this->newPlaylist();
        $nbPlaylists = $repository->count([]);
        $repository->add($playlist, true);
        $this->assertEquals($nbPlaylists + 1, $repository->count([]), "erreur lors de l'ajout");
    }
    
    /**
     * Teste la suppression d'une playlist.
     */
    public function testRemovePlaylist()
    {
        $repository = $this->recupRepository();
        $playlist = $this->newPlaylist();
        $repository->add($playlist, true);
        $nbPlaylists = $repository->count([]);
        $repository->remove($playlist, true);
        $this->assertEquals($nbPlaylists - 1, $repository->count([]), "erreur lors de la suppression");
    }
    
    /**
     * Teste la méthode findAllOrderByNombreFormations.
     */
    public function testFindAllOrderByNombreFormations()
    {
        $repository = $this->recupRepository();

        // Récupère toutes les playlists triées par le nombre de formations en ordre croissant
        $playlists = $repository->findAllOrderByNombreFormations('ASC');

        // Vérifie que la première playlist a le plus de formations
        $this->assertGreaterThanOrEqual(
            $playlists[0]->getFormations()->count(),
            $playlists[1]->getFormations()->count(),
            "La première playlist devrait avoir le plus de formations"
        );

        // Vérifie que la deuxième playlist a plus de formations que la troisième
        $this->assertGreaterThanOrEqual(
            $playlists[1]->getFormations()->count(),
            $playlists[2]->getFormations()->count(),
            "La deuxième playlist devrait avoir plus de formations que la troisième"
        );
    }
    
}
