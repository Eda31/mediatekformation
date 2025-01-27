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
     * @const pour la page des playlists de la partie admin
     */
    private const PAGE_ADMIN_PLAYLISTS = "admin/admin.playlists.html.twig";
    
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
    public function __construct(PlaylistRepository $playlistRepository,
            CategorieRepository $categorieRepository,
            FormationRepository $formationRespository)
    {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    /**
     * Affiche toutes les playlists.
     * @return Response
     */
    #[Route("/admin/playlists", name: "admin.playlists")]
    public function index(): Response
    {
        // Récupérer toutes les playlists triées par nom
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        
        // Rendre la vue avec les playlists et les catégories
        return $this->render(self::PAGE_ADMIN_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
     * pour supprimer une playlist
     * @param Playlist $playlitst
     * @return Response
     */
    #[Route("/admin/playlist/suppr/{id}", name: "admin.playlist.suppr")]
    public function suppr(Playlist $playlist): Response
    {
        $this->playlistRepository->remove($playlist, true);
        return $this->redirectToRoute('admin.playlists');
    }
    
    /**
     * pour modifier une playlist existante
     * @param Playlist $playlist
     * @param Request $request
     * @return Response
     */
    #[Route("/admin/playlist/edit/{id}",name:"admin.playlist.edit")]
    public function edit(Playlist $playlist, Request $request): Response
    {
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        
        $formPlaylist->handleRequest($request);
        if ($formPlaylist->isSubmitted() && $formPlaylist->isValid()) {
            $this->playlistRepository->add($playlist, true);
            return $this->redirectToRoute('admin.playlists');
        }
        
        return $this->render("admin/admin.playlist.edit.html.twig", [
            'playlist' => $playlist,
            'formplaylist' => $formPlaylist->createView()
        ]);
    }
    
    /**
     * pour ajouter une nouvelle playlist
     * @param Request $request
     * @return Response
     */
    #[Route("/admin/playlist/ajout",name:"admin.playlist.ajout")]
    public function ajout(Request $request): Response
    {
        $playlist = new Playlist();
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        
        $formPlaylist->handleRequest($request);
        if ($formPlaylist->isSubmitted() && $formPlaylist->isValid()) {
            $this->playlistRepository->add($playlist, true);
            return $this->redirectToRoute('admin.playlists');
        }
        
        return $this->render("admin/admin.playlist.ajout.html.twig", [
            'playlist' => $playlist,
            'formplaylist' => $formPlaylist->createView()
        ]);
    }
    /**
     * pour trier les playlists dans l'ordre souhaité
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    #[Route('/admin/playlists/tri/{champ}/{ordre}', name: 'admin.playlists.sort')]
    public function sort($champ, $ordre): Response
    {
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
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_ADMIN_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    /**
     * pour rechercher les playlists souhaité
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    #[Route('/admin/playlists/recherche/{champ}/{table}', name: 'admin.playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_ADMIN_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
}