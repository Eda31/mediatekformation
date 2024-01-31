<?php

namespace App\Controller\admin;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of AdminCategoriesController
 * @author Jadem
 */
class AdminCategoriesController extends AbstractController
{
    /**
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    /**
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }
    
    /**
     * @Route("/admin/categories", name="admin.categories")
     * @return Response
     */
    public function index(): Response
    {
        $categories = $this->categorieRepository->findAll();
        return $this->render("admin/admin.categories.html.twig", [
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("/admin/categorie/suppr/{id}", name="admin.categorie.suppr")
     * @param Categorie $categorie
     * @return Response
     */
    public function suppr(Categorie $categorie): Response
    {
        $formations = $categorie->getFormations();

    if ($formations->isEmpty()) {
        $this->categorieRepository->remove($categorie, true);
        $this->addFlash('success', 'Catégorie supprimée avec succès.');
    } else {
        $this->addFlash('error', 'La catégorie ne peut pas être supprimée car elle est associée à des formations.');
    }

    return $this->redirectToRoute('admin.categories');
    }
    
    /**
     * @Route("/admin/categorie/ajout", name="admin.categorie.ajout")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response
    {
        $nomCategorie = $request->get("nom");
        // Vérifier si la catégorie avec ce nom existe déjà
        $existCategorie = $this->categorieRepository->findOneBy(['name' => $nomCategorie]);
        
        if ($existCategorie) {
            $this->addFlash('error', 'Une categorie avec ce nom existe déjà.');
        } else {
            $categorie = new Categorie();
            $categorie->setName($nomCategorie);
            $this->categorieRepository->add($categorie, true);
            $this->addFlash('success', 'Catégorie ajoutée avec succés.');
        }
        
        return $this->redirectToRoute('admin.categories');
    }
}
