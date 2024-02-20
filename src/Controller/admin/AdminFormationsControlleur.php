<?php

namespace App\Controller\admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of AdminFormationsControlleur
 * Contrôleur gérant l'administration des formations.
 * @author Jadem
 */
class AdminFormationsControlleur extends AbstractController
{
    /**
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    /**
     * @param FormationRepository $formationRepository
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    /**
     * Affiche toutes les formations.
     * @Route("/admin", name="admin.formations")
     * @return Response
     */
    public function index(): Response
    {
        // Récupérer toutes les formations
        $formations = $this->formationRepository->findAll();
        
        // Récupérer toutes les catégories
        $categories = $this->categorieRepository->findAll();
        
        // Afficher la vue avec la liste des formations et des catégories
        return $this->render("admin/admin.formations.html.twig", [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * Trie et affiche les formations en fonction des paramètres de tri.
     * @Route("/admin/formations/tri/{champ}/{ordre}/{table}", name="admin.formations.sort")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sort($champ, $ordre, $table=""): Response
    {
        // Récupérer les formations triées en fonction des paramètres
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        
        // Récupérer toutes les catégories
        $categories = $this->categorieRepository->findAll();
        
        // Afficher la vue avec la liste triée des formations et des catégories
        return $this->render("admin/admin.formations.html.twig", [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * Recherche et affiche les formations qui contiennent une valeur spécifique.
     * @Route("/admin/formations/recherche/{champ}/{table}", name="admin.formations.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        // Récupérer la valeur de recherche depuis la requête
        $valeur = $request->get("recherche");
        
        // Récupérer les formations qui contiennent la valeur spécifique
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        
        // Récupérer toutes les catégories
        $categories = $this->categorieRepository->findAll();
        
        // Afficher la vue avec la liste des formations filtrées par la valeur de recherche et des catégories
        return $this->render("admin/admin.formations.html.twig", [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * Affiche les détails d'une formation spécifique.
     * @Route("/admin/formations/formation/{id}", name="admin.formations.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response
    {
        // Récupérer la formation spécifique
        $formation = $this->formationRepository->find($id);
        
        // Afficher la vue avec les détails de la formation
        return $this->render("admin/admin.formation.html.twig", [
            'formation' => $formation
        ]);
    }
    /**
     * Supprime une formation spécifique.
     * @Route("/admin/suppr/{id}", name="admin.formation.suppr")
     * @param Formation $formation
     * @return Response
     */
    public function suppr(Formation $formation): Response
    {
        // Supprimer la formation spécifique
        $this->formationRepository->remove($formation, true);
        
        // Rediriger vers la liste des formations
        return $this->redirect('admin.formation');
    }
    /**
     * Édite une formation spécifique.
     * @Route("/admin/edit/{id}", name="admin.formation.edit")
     * @param Formation $formation
     * @param Request $request
     * @return Response
     */
    public function edit(Formation $formation, Request $request): Response
    {
        // Créer le formulaire d'édition de formation
        $formFormation = $this->createForm(FormationType::class, $formation);
        
        // Gérer la soumission du formulaire
        $formFormation->handleRequest($request);
        if ($formFormation->isSubmitted() && $formFormation->isValid()) {
            // Enregistrer les modifications de la formation
            $this->formationRepository->add($formation, true);
            
            // Rediriger vers l'édition de la formation
            return $this->redirectToRoute('admin.formation.edit', ['id' => $formation->getId()]);
        }
        // Rendre la vue avec le formulaire d'édition de la formation
        return $this->render("admin/admin.formation.edit.html.twig", [
            'formation' => $formation,
            'formformation' => $formFormation->createView()
        ]);
    }
    /**
     * Ajoute une nouvelle formation.
     * @Route("/admin/ajout", name="admin.formation.ajout")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response
    {
        // Créer une nouvelle instance de Formation
        $formation = new Formation();
        
        // Créer le formulaire associé à l'entité Formation
        $formFormation = $this->createForm(FormationType::class, $formation);
        
        // Gérer la soumission du formulaire
        $formFormation->handleRequest($request);
        // Vérifier si le formulaire a été soumis et est valide
        if ($formFormation->isSubmitted() && $formFormation->isValid()) {
            // Ajouter la nouvelle formation à la base de données
            $this->formationRepository->add($formation, true);
            
            // Rediriger vers la page d'édition de la formation nouvellement ajoutée
            return $this->redirectToRoute('admin.formation.edit', ['id' => $formation->getId()]);
        }
        
        // Rendre la vue avec le formulaire d'ajout de la formation
        return $this->render("admin/admin.formation.ajout.html.twig", [
            'formation' => $formation,
            'formformation' => $formFormation->createView()
        ]);
    }
}
