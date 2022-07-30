<?php
    namespace App\Services;
    
    use DateTime;
    use App\Entity\Commande; 
    use App\Entity\Detailcommande;

    use Symfony\Component\Security\Core\Security;
    

    class CommandeManager
    {
        private $security;

        public function __construct(Tools $security) 
        {
            $this->security = $security;
        }
    
        /**
         * Methode validation Commande
         *
         * @param Panier $panier
         * @return Commande
         * 
         */
        public function getCommande(Panier $panier)
        {
            // Je cree un nouvel objet vide 
            $commande = new Commande();

            // je set le user connecté
            $user = $this->security->getUser();
            
            // je set la commande avec le user connecté}
            $commande->setUser($user);

            // Je recupere la date du jour 
            $date_jour = new DateTime();
            
            date_timezone_set($date_jour, timezone_open('Europe/Paris'));


            $commande->setDateCommande($date_jour);
            $commande->setNom($user->getNom());

            $adresse = $user->getAdresse() . ' ' . $user->getCpost() . ' ' . $user->getVille();  
            $commande -> setAdresse($adresse);

            $commande->setTotal($panier->getTotalPanier());
            $commande->setExped(false);
            $commande->setEmail($user->getEmail());

            return $commande;
        }

        public function getDetailCommande(Commande $commande, $row_panier)
        {
            $detailCommande = new DetailCommande();

            // setter l'objet commande a mon detailcommande 

            $detailCommande -> setCommande($commande); 
            
            // je set la quantite 
            $detailCommande -> setQuantity($row_panier['quantity']); 

            $detailCommande -> setTitre($row_panier['produit']->getTitre()); 

            $detailCommande -> setPrixUnit($row_panier['produit']->getPrixUnit()); 
            
            $detailCommande -> setTotal($row_panier['total']); 
            
            $detailCommande -> setRef($row_panier['produit']->getRef()); 

            return $detailCommande;

        }
        
           


    }