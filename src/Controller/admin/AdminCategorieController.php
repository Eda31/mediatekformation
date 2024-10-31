<?php

namespace App\Controller\admin;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of AdminCategorieController
 * @author Jadem
 */
class AdminCategorieController extends AbstractController
{
    /**
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    /**
     * Constructeur du contrôleur avec injection de dépendance.
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }
    
    /**
     * Affiche toutes les catégories.
     * @return Response
     */
    #[Route('/admin/categories', name: 'admin.categories')]
    public function index(): Response
    {
        // Récupérer toutes les catégories depuis le repository
        $categories = $this->categorieRepository->findAll();
        
        // Rendre la vue avec les catégories
        return $this->render("admin/admin.categories.html.twig", [
            'categories' => $categories
        ]);
    }
    
    /**
     * Supprime une catégorie.
     * @param Categorie $categorie
     * @return Response
     */
    #[Route('/admin/categorie/suppr/{id}', name:'admin.categorie.suppr')]
    public function suppr(Categorie $categorie): Response
    {
        // Récupérer les formations liées à la catégorie
        $formations = $categorie->getFormations();
        
        // Vérifier si des formations sont associées à la catégorie
        if ($formations->isEmpty()) {
            // Supprimer la catégorie
            $this->categorieRepository->remove($categorie, true);
            $this->addFlash('success', 'Catégorie supprimée avec succès.');
        } else {
            // Afficher un message d'erreur si des formations sont associées
            $this->addFlash('error', 'La catégorie ne peut pas être supprimée car elle est associée à des formations.');
        }
        // Rediriger vers la liste des catégories
        return $this->redirectToRoute('admin.categories');
    }
    
    /**
     * Ajoute une nouvelle catégorie.
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/categorie/ajout', name:'admin.categorie.ajout')]
    public function ajout(Request $request): Response
    {
        // Récupérer le nom de la catégorie depuis la requête
        $nomCategorie = $request->get("nom");
        // Vérifier si une catégorie avec le même nom existe déjà
        $existCategorie = $this->categorieRepository->findOneBy(['name' => $nomCategorie]);
        
        // Ajouter la nouvelle catégorie ou afficher un message d'erreur si elle existe déjà
        if ($existCategorie) {
            $this->addFlash('error', 'Une categorie avec ce nom existe déjà.');
        } else {
            $categorie = new Categorie();
            $categorie->setName($nomCategorie);
            $this->categorieRepository->add($categorie, true);
            $this->addFlash('success', 'Catégorie ajoutée avec succés.');
        }
        // Rediriger vers la liste des catégories
        return $this->redirectToRoute('admin.categories');
    }
}
