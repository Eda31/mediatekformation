<?php

namespace App\Controller\admin;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of AdminPlaylistsController
 * Contrôleur gérant l'administration des playlists.
 * @author Jadem
 */
class AdminPlaylistsController extends AbstractController
{
    /**
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
    /**
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    /**
     * Constructeur du contrôleur avec injection de dépendance.
     * @param PlaylistRepository $playlistRepository
     * @param CategorieRepository $categorieRepository
     * @param FormationRepository $formationRespository
     */
    public function __construct(
            PlaylistRepository $playlistRepository,
            CategorieRepository $categorieRepository,
            FormationRepository $formationRespository
            ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    /**
     * Affiche toutes les playlists.
     * @Route("/admin/playlists", name="admin.playlists")
     * @return Response
     */
    public function index(): Response
    {
        // Récupérer toutes les playlists triées par nom
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        
        // Rendre la vue avec les playlists et les catégories
        return $this->render("admin/admin.playlists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    
    /**
     * Trie les playlists en fonction du champ spécifié et de l'ordre.
     * @Route("/admin/playlists/tri/{champ}/{ordre}", name="admin.playlists.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response
    {
        // Trie les playlists en fonction du champ spécifié
        switch ($champ) {
            case "name":
                $playlists = $this->playlistRepository->findAllOrderByName($ordre);
                break;
            case "nombre_formations":
                $playlists = $this->playlistRepository->findAllOrderByNombreFormations($ordre);
                break;
            default :
                $playlists = [];
        }
        // Récupère toutes les catégories
        $categories = $this->categorieRepository->findAll();
        
        // Rendre la vue avec les playlists triées et les catégories
        return $this->render("admin/admin.playlists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    /**
     * Recherche les playlists en fonction du champ spécifié et de la valeur fournie.
     * @Route("/admin/playlists/recherche/{champ}/{table}", name="admin.playlists.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        // Récupérer la valeur de recherche depuis la requête
        $valeur = $request->get("recherche");
        
        // Rechercher les playlists en fonction du champ et de la valeur spécifiés
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        
        // Récupérer toutes les catégories
        $categories = $this->categorieRepository->findAll();
        
        // Rendre la vue avec les playlists résultantes, les catégories, la valeur de recherche et la table
        return $this->render("admin/admin.playlists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * Affiche les détails d'une playlist spécifique.
     * @Route("/admin/playlists/playlist/{id}", name="admin.playlists.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response
    {
        // Récupérer la playlist et les catégories associées
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        
        // Rendre la vue avec les détails de la playlist, les catégories et les formations associées
        return $this->render("admin/amdin.playlist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }
    
    /**
     * Supprime une playlist.
     * @Route("/admin/playlist/suppr/{id}", name="admin.playlist.suppr")
     * @param Playlist $playlist
     * @return Response
     */
    public function suppr(Playlist $playlist): Response
    {
        // Récupérer les formations liées à la playlist
        $formations = $playlist->getFormations(); // Vérifier si des formations sont associées à la playlist
        
        // Vérifier si des formations sont associées à la playlist
        if ($formations->isEmpty()) {
            // Supprimer la playlist
            $this->playlistRepository->remove($playlist, true);
            $this->addFlash('success', 'Playlist supprimée avec succès.');
        } else {
            // Afficher un message d'erreur si des formations sont associées
            $this->addFlash('error', 'La suppression n\'est pas possible car des formations sont associées à cette playlist.');
        }
        // Rediriger vers la liste des playlists
        return $this->redirectToRoute('admin.playlists');
    }
    
    /**
     * Édite une playlist existante.
     * @Route("/admin/playlist/edit/{id}", name="admin.playlist.edit")
     * @param Playlist $playlist
     * @param Request $request
     * @return Response
     */
    public function edit(Playlist $playlist, Request $request): Response
    {
        // Créer le formulaire pour éditer la playlist
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        
        // Gérer la soumission du formulaire
        $formPlaylist->handleRequest($request);
        if ($formPlaylist->isSubmitted() && $formPlaylist->isValid()) {
            // Enregistrer les modifications de la playlist
            $this->playlistRepository->add($playlist, true);
            
            // Rediriger vers l'édition de la playlist
            return $this->redirectToRoute('admin.playlist.edit', ['id' => $playlist->getId()]);
        }
        
        return $this->render("admin/admin.playlist.edit.html.twig", [
            'playlist' => $playlist,
            'formplaylist' => $formPlaylist->createView()
        ]);
    }
    
    /**
     * Ajoute une nouvelle playlist.
     * @Route("/admin/playlist/ajout", name="admin.playlist.ajout")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response
    {
        // Créer une nouvelle instance de Playlist
        $playlist = new Playlist();
        
        // Créer le formulaire d'ajout de playlist
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        
        // Gérer la soumission du formulaire
        $formPlaylist->handleRequest($request);
        if ($formPlaylist->isSubmitted() && $formPlaylist->isValid()) {
            // Enregistrer la nouvelle playlist
            $this->playlistRepository->add($playlist, true);
            
            // Rediriger vers l'édition de la nouvelle playlist
            return $this->redirectToRoute('admin.playlist.edit', ['id' => $playlist->getId()]);
        }
        // Rendre la vue avec le formulaire d'ajout de playlist
        return $this->render("admin/admin.playlist.ajout.html.twig", [
            'playlist' => $playlist,
            'formplaylist' => $formPlaylist->createView()
        ]);
    }
}
