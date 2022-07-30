<?php

namespace App\Controller\Admin;

use App\Entity\Photos;
use App\Entity\Produit;
use App\Form\PhotosType;
use App\Form\ProduitType;
use App\Repository\PhotosRepository;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/produit")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="app_produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_produit_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProduitRepository $produitRepository): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ici code a ajouter
            // ici je get le fichier qui a était chargé dans le formulaire
            $img = $form->get('img')->getData();
            // je test si y a bien eut un chargement d'image 

            if ($img) {
                // si oui 
                // je donne un nom unique au fichier 
                $new_name_img = uniqid() . '.'  . $img->guessExtension();
                // et je l'envoie au dossier sur le serveur 
                $img->move($this->getParameter('upload_dir'), $new_name_img);
                // je set l'objet article avec le nouveau nom
                $produit->setImg($new_name_img);
            } else {
                //si non
                // je set l'article avec l'image par default 
                $produit->setImg('defaultImg.png');
            }

 
            $ref = uniqid();
            $produit->setRef($ref);

            // ici fin de code a ajouter
            $produitRepository->add($produit, true);

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/voirphoto/{id}", name="app_produit_show")
     */
    public function show(Request $request, PhotosRepository $photosRepository, Produit $produit): Response
    {
        $photo = new Photos();
        $form = $this->createForm(PhotosType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $img = $form->get('name')->getData();
            // je test si y a bien eut un chargement d'image 

            if ($img)
            {
                // si oui 
                // je donne un nom unique au fichier 
                $new_name_img = uniqid() . '.'  . $img->guessExtension();
                // et je l'envoie au dossier sur le serveur 
                $img->move($this->getParameter('upload_dir'), $new_name_img);
                // je set l'objet article avec le nouveau nom
                $photo->setName($new_name_img);
                // TODO ici supprimer l'ancienne image    
            }

           $photo->setProduit($produit); 
           $photosRepository->add($photo, true); 

           return $this->redirectToRoute('app_produit_show', ['id' =>$produit->getId()]); 
        }

        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'form' => $form->createview()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_produit_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Produit $produit, ProduitRepository $produitRepository): Response
    {
        
        $old_name = $produit->getImg();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             // ici je get le fichier qui a était chargé dans le formulaire
             $img = $form->get('img')->getData();
             // je test si y a bien eut un chargement d'image 
 
             if ($img) {
                 // si oui 
                 // je donne un nom unique au fichier 
                 $new_name_img = uniqid() . '.'  . $img->guessExtension();
                 // et je l'envoie au dossier sur le serveur 
                 $img->move($this->getParameter('upload_dir'), $new_name_img);
                 // je set l'objet article avec le nouveau nom
                 $produit->setImg($new_name_img);
                 // TODO ici supprimer l'ancienne image 
                    
                 $path = $this->getParameter('upload_dir') . $old_name;
                 unlink($path);

             } else {
                 //si non
                 // je set l'article avec l'image par default 
                 $produit->setImg($old_name);
             }
            

            $produitRepository->add($produit, true);

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_produit_delete", methods={"POST"})
     */
    public function delete(Request $request, Produit $produit, ProduitRepository $produitRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->request->get('_token'))) {
            $produitRepository->remove($produit, true);
        }
        
        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}
