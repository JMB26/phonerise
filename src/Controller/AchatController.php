<?php

namespace App\Controller;

use Stripe\Price;
use Stripe\Stripe;
use App\Services\Tools;
use App\Entity\Commande;
use App\Services\Panier;
use Stripe\Checkout\Session;
use App\Form\ConfirmeUserType;
use App\Services\CommandeManager;
use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use App\Repository\DetailCommandeRepository;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AchatController extends AbstractController
{
    /**
     * @Route("/achat", name="app_achat")
     */
    public function index(UserRepository $userRepository, Request $request, Panier $panier, Tools $tools): Response
    {
        //  Je recupere les donnes du User connecté

        $user = $tools->getUser();

       

        if ($user)
        {   
            // l'Erreur est juste une erreur de VS code mais le compilateur du serveur la trouvera à l'execution... 
            if (is_null($user->getNom()))
            {
                // ConfirmeUserType::class = adresse de la classe (namespace)
                $form = $this->createForm(ConfirmeUserType::class, $user);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid())
                {
                    $userRepository->add($user, true);
                    return $this->redirectToRoute('app_achat');
                }
                return $this->render('achat/modifyUser.html.twig', [
                    'form' => $form->createView()
                ]);
            }
        }else{
            return $this->RedirectToRoute('app_login');
            // dd($user);
        }

        return $this->render('achat/index.html.twig', [
            'panier' => $panier->getDetailPanier(),
            'total' => $panier->getTotalPanier(),
            'totalpx' => $panier->getTotPxPanier(),
        ]);
    }


    /**
     * @Route("/commande", name="app_new_commande")
     */    
    public function commande(Panier $panier, CommandeManager $manager, CommandeRepository $commandeRepository, DetailCommandeRepository $detailCommandeRepository): Response
    {
        $commande = $manager->getCommande($panier);
        // $commandeRepository->add($commande,true);

        // pour les tests on enregistre pas... 

        $commandeRepository->add($commande);

        $detailPanier = $panier->getDetailPanier();

        foreach ($detailPanier as $row_panier) {
            $detailCommande = $manager->getDetailCommande($commande, $row_panier);
            //    on stocke les articles 
            $detailCommandeRepository->add($detailCommande);
        }

        // on execute en 1 fois la sauvegarde
        $detailCommandeRepository->add($detailCommande, true);

        // TODO code commenté pour les tests.....

        // ------------------------------------------

        // $stripe= new \Stripe\StripeClient('sk_test_51LL1ZLHc2upO85uDyyLzoedkqTc2sfj5DE4ceyxxZnSZGucJfhD6znmIaeWfUXqN9NwyendiKFrtobNsHb6Jc0KR00Gc4DpN3M');

        // Pour le paiement On ajoute ici le code copié sur stripe : https://stripe.com/docs/checkout/quickstart
        // on suprime les chemins des class et à la place on importe les class Stripe, Session 

        // ----------------------------------------


        // This is your test secret API key.
        // Stripe::setApiKey('sk_test_51LL1ZLHc2upO85uDyyLzoedkqTc2sfj5DE4ceyxxZnSZGucJfhD6znmIaeWfUXqN9NwyendiKFrtobNsHb6Jc0KR00Gc4DpN3M');

        // header('Content-Type: application/json');

        // $YOUR_DOMAIN = 'http://localhost:8000';

        // $price=[];
        // $line_items = [];
        // $i=0;

        // foreach ($detailPanier as $row)
        // {           
        //     $product[] = $stripe->products->create([
        //         'name' => $row['produit']->getTitre(),
        //         'images' => ["https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSBBt_q7dOooBXjFNuuNoOqUA_lCkql-Pfw0oFI0gS_rWcBYjs5f3pX7OrstoJnTwtJa3Q&usqp=CAU"]
        
        // ]);
                       
        //     $price[] = Price::create([
        //         'product' => $product[$i]->values()[0],
        //         'unit_amount' => $row['produit']->getPrixUnit()*100,
        //         'currency' => 'eur'
        //     ]);

        //     $line_items[] = [ 
        //         'price' =>  $price[$i]->values()[0],
        //         'quantity' => $row['quantity']
        //         ];

        //     $i++;
        // }
        
        // $checkout_session = Session::create([
            
        //     'line_items' => [$line_items],
        //     'mode' => 'payment',
        //     // 'success_url' => $YOUR_DOMAIN . '/success' . '/' . $commande->getId(),
        //     'success_url' => $YOUR_DOMAIN . '/success',
        //     // 'cancel_url' => $YOUR_DOMAIN . '/cancel/' . '/' . $commande->getId(),
        //     'cancel_url' => $YOUR_DOMAIN . '/cancel',
        // ]);

        // suppression panier        
        // $panier->deletePanier();
        
        // $response = new RedirectResponse($checkout_session->url);
        // return $response; 

        // ----------------------------------------------

        // Pour les tests on remplace par :
        // suppression panier
        // --------------------------
        $panier->deletePanier();
        return $this->RedirectToRoute('app_home');
        // -----------------------------


    }

    
    /**
     * @Route("/cancel/{id}", name="app_cancel_stripe")
     */
    public function cancel(Commande $commande, CommandeRepository $commandeRepository ): Response
    {        

        $commandeRepository->remove($commande,true);
        return $this->render('stripe/cancel.html', []);
    }

    /**
     * @Route("/success/{id}", name="app_success_stripe")
     */
    public function success(Commande $commande, Panier $panier, ProduitRepository $produitRepository): Response
    {
        // Décrémenter le stock 

        $detailCommande = $commande->getDetailCommandes();

        foreach ($detailCommande as $row)
        {
            // je recupere la ref produit
           $ref = $row->getRef();

            // je recupere le produit vendu    
            $produit = $produitRepository->findOneBy(['ref' => $ref]);
            // dd($produit);

            // Qu. vendue
            $quantity_vendu = $row->getQuantity();
            
            // New Qu. Stock
            $new_quantity = $produit->getQuantity() - $quantity_vendu; 
            
            // j'enregistre la modif
            $produit->setQuantity($new_quantity);

            $produitRepository->add($produit,true);

        }


        // suppression panier
        $panier->deletePanier();

        return $this->render('stripe/success.html', [
          'commande' => $commande,
    
        ]);
    }


}
