<?php

namespace App\Controller\Admin;

use App\Entity\Carrousel;
use App\Form\CarrouselType;
use App\Repository\ProduitRepository;
use App\Repository\CarrouselRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/carrousel")
 */
class CarrouselController extends AbstractController
{
    /**
     * @Route("/", name="app_carrousel_index", methods={"GET"})
     */
    public function index(CarrouselRepository $carrouselRepository): Response
    {
        return $this->render('carrousel/index.html.twig', [
            'carrousels' => $carrouselRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_carrousel_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CarrouselRepository $carrouselRepository): Response
    {
        $carrousel = new Carrousel();
        $form = $this->createForm(CarrouselType::class, $carrousel);
        $form->handleRequest($request);
      

        if ($form->isSubmitted() && $form->isValid()) {
            // ici je get le fichier qui a était chargé dans le formulaire
            $img = $form->get('name')->getData();
            // je test si y a bien eut un chargement d'image 

            if ($img) {
                // si oui 
                // je donne un nom unique au fichier 
                $new_name_img = uniqid() . '.'  . $img->guessExtension();
                // et je l'envoie au dossier sur le serveur 
                $img->move($this->getParameter('upload_dir'), $new_name_img);
                // je set l'objet article avec le nouveau nom
                $carrousel->setName($new_name_img);
                // TODO ici supprimer l'ancienne image                    
            }

           $carrousel->setStatus(false); 
           $carrouselRepository->add($carrousel, true);

           return $this->redirectToRoute('app_carrousel_index', [], Response::HTTP_SEE_OTHER);
       }

        return $this->renderForm('carrousel/new.html.twig', [
            'carrousel' => $carrousel,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_carrousel_show", methods={"GET"})
     */
    public function show(Carrousel $carrousel): Response
    {
        return $this->render('carrousel/show.html.twig', [
            'carrousel' => $carrousel,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_carrousel_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Carrousel $carrousel, CarrouselRepository $carrouselRepository): Response
    {
        $form = $this->createForm(CarrouselType::class, $carrousel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $carrouselRepository->add($carrousel, true);

            return $this->redirectToRoute('app_carrousel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('carrousel/edit.html.twig', [
            'carrousel' => $carrousel,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_carrousel_delete", methods={"POST"})
     */
    public function delete(Request $request, Carrousel $carrousel, CarrouselRepository $carrouselRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$carrousel->getId(), $request->request->get('_token'))) {
            $carrouselRepository->remove($carrousel, true);
        }

        return $this->redirectToRoute('app_carrousel_index', [], Response::HTTP_SEE_OTHER);
    }



     /**
     * @Route("/valid-affichage-carrousel/{id}", name="app_valider_carrousel")
     */
    public function status(Carrousel $carrousel, CarrouselRepository $carrouselRepository): Response
    {
        $status = $carrousel->isStatus();

        if ($status)
        {
            $carrousel->setStatus(false);
        }else{
            $carrousel->setStatus(true);
        }
        
        $carrouselRepository->add($carrousel, true);

        return $this->redirectToRoute('app_carrousel_index', []);

        // return $this->render('commande/show.html.twig', [
        //     'commande' => $commande,
        // ]);
    }
}
