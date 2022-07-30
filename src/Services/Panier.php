<?php

namespace App\Services;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Panier
{
    private $session;

    private $produitRepository; 

    public function __construct(SessionInterface $session, ProduitRepository $produitRepository)
    {
        $this->session = $session;
        $this->produitRepository = $produitRepository; 
    }


    /**
     * Undocumented function
     *
     * @param [type] $id
     * @return void
     */
    public function addPanier($id)    
    // public function addPanier($id,$q)    
    //  on peut aussi passer directement une quantité avec une variable $q
    {
        $panier = $this->session->get('panier',[]);
        //  On teste si l'Id existe
        if(!empty($panier[$id]))
        {
           $panier[$id]= $panier[$id]+1; 
        //   avec $qu on a  $panier[$id]= $panier[$id]+$q; 
        }else
        {
            // S'il n'existe pas on l'ajoute au tableau $panier
            $panier[$id]= 1 ;
        }
        // On enregistre dans la panier 
        $this-> session->set('panier',$panier);
    }

// 

    /**
     * methode Renvoie le panier
     *
     * @return array
     */
    public function getPanier()
    {
        return $this->session->get('panier',[]);
    }


    // on configure avec l'extension blocker pour l'afficher direct 
    /**
     * methode supprime le panier
     *
     * @param integer $id  
     * @return void
     */

    // on ajoute l'extension blocker pour afficher direct la notification


    public function deleteProduitPanier($id)
    {
        $panier = $this->session->get('panier',[]);

        //  On teste si l'Id existe
        if(!empty($panier[$id]))
        {
           unset($panier[$id]);         
        }
        return $this->session->set('panier',$panier);        
    }


    /**
     * Décrémenter un article panier
     *
     * @param [type] $id
     * @return void
     */
    public function retireQuantityProduct($id)        
    {
        $panier = $this->getPanier();
        
        //  On teste si l'Id existe
        if(!empty($panier[$id]))
        {
            //  On teste si la quantité est > 1
            if($panier[$id] > 1)
            {
            $panier[$id]= $panier[$id] - 1; 
            
            }else
            {
                // Sinon on le supprime du panier
                unset($panier[$id]) ;
            }
            // On enregistre dans la panier 
            $this-> session->set('panier',$panier);
        }
    }

    /**
     * Supprimer le panier
     *
     * @return void
     */
    public function deletePanier()
    {     
        return $this->session->remove('panier');        
    }

     /**
     * methode Renvoie le detail du panier
     *
     * 
     */
    public function getDetailPanier()
    {
        $panier = $this->getPanier();
        $detail_panier = [];

        foreach ($panier as $id => $quantity)
        {   
            $produit = $this->produitRepository->find($id);

            if (!is_null($produit)) {
                $detail_panier [] = [                
                    'produit' => $produit,
                    'quantity'=> $quantity,
                    'total' => $produit->getPrixUnit() * $quantity 
                    // 'ht' => 'ht',


                ];     
            }
                       
        }
        return $detail_panier;
    }

    /**
     * methode Renvoie le total du panier
     *
     * 
     */
    public function getTotalPanier()
    {
        $panier = $this->getPanier();
        $total_panier = 0;

        foreach ($panier as $id => $quantity)
        {
            $produit = $this->produitRepository->find($id);

            if (!is_null($produit)) {
                $total_panier = $total_panier + $quantity;  
            }  
        }      
        
        return $total_panier;        
    }

    public function getTotPxPanier()
    {
        $panier = $this->getPanier();
        $total_px = 0;

        foreach ($panier as $id => $quantity)
        {

            $produit = $this->produitRepository->find($id);

            if (!is_null($produit)) {
                $total_px = $total_px + $produit->getPrixUnit() * $quantity;    
            }
        }      
        
        return $total_px;        
    }



}