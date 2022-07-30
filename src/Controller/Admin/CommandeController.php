<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Config\Monolog\HandlerConfig\ElasticsearchConfig;

/**
 * @Route("/admin/commande")
 */
class CommandeController extends AbstractController
{
    // /**
    //  * @Route("/", name="app_commande_index", methods={"GET"})
    //  */
    // public function index(CommandeRepository $commandeRepository): Response
    // {
    //     return $this->render('commande/index.html.twig', [
    //         'commandes' => $commandeRepository->findAll(),
    //     ]);
    // }

    // Nouvelle methode avec un index pour pouvoir filtrer
    /**
     * @Route("/{param}", name="app_commande_index", defaults={"param": null})
     */
    public function index(CommandeRepository $commandeRepository,$param): Response
    {
        if ($param !== null)
        {
            $commandes = $commandeRepository->findBy(['exped'=> $param]);
        }else
        {
            $commandes = $commandeRepository->findAll();
        }

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes
        ]);
    }

    // Methode separee pour filtrer les commandes livrees remplacee par index avec ou sans parametre
    // ATTENTION La position de la methode dans le controller peut creer une erreur  
    /**
     * @Route("/livree", name="app_commande_livree", methods={"GET"})
     */
    public function livree(CommandeRepository $commandeRepository): Response
    {
       
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findby(['exped'=> true]),
        ]);
    }

    // Methode separee pour filtrer les commandes on livrees remplacee par index avec ou sans parametre
    /**
     * @Route("/attente", name="app_commande_attente", methods={"GET"})
     */
    public function attente(CommandeRepository $commandeRepository): Response
    {
       
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findby(['exped'=> false]),
        ]);
    }

    /**
     * @Route("/new", name="app_commande_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CommandeRepository $commandeRepository): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandeRepository->add($commande, true);

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/voir/{id}", name="app_commande_show", methods={"GET"})
     */
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_commande_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Commande $commande, CommandeRepository $commandeRepository): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandeRepository->add($commande, true);

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_commande_delete", methods={"POST"})
     */
    public function delete(Request $request, Commande $commande, CommandeRepository $commandeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $commandeRepository->remove($commande, true);
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/expedition", name="app_commande_expedition", methods={"GET"})
     */
    public function expedition(Commande $commande, CommandeRepository $commandeRepository): Response
    {

        $commande->setExped(true);
        $commandeRepository->add($commande, true);

        return $this->redirectToRoute('app_commande_index', []);

        // return $this->render('commande/show.html.twig', [
        //     'commande' => $commande,
        // ]);
    }

   
}
