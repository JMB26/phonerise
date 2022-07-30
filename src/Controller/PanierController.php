<?php

namespace App\Controller;

use App\Services\Panier;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="app_panier")
     */
    public function index(Panier $panier): Response
    {
        // $panier->addPanier(1);
        // $panier->addPanier(2);
        // $panier->addPanier(3);
        // $panier->deleteProduitPanier(5);
        // $panier->retireQuantityProduct(5);
        // $panier->deletePanier();

        return $this->render('panier/index.html.twig', [
            // 'panier' => $panier->getPanier() On remplace par la methode detailpanier           
            'panier' => $panier->getDetailPanier(),                     
            'total' => $panier->getTotalPanier(),                     
            'totalpx' => $panier->getTotPxPanier(),                     

        ]);
    }

    /**
     * @Route("/ajouter-panier/{id}", name="app_add_panier")
     */
    public function addpanier($id, Panier $panier): Response
    {       
        $panier->addPanier($id);
        return $this->redirectToRoute('app_panier');
    }

/**
     * @Route("/retire-produit/{id}", name="app_retire_quantity")
     */
    public function retireQuantityProduct($id, Panier $panier): Response
    {       
        $panier->retireQuantityProduct($id);
        return $this->redirectToRoute('app_panier');
    }

    /**
     * @Route("/delete-panier/{id}", name="app_del_produit_panier")
     */
    public function deleteProduitPanier($id, Panier $panier): Response
    {       
        $panier->deleteProduitPanier($id);
        return $this->redirectToRoute('app_panier');
    }
}
