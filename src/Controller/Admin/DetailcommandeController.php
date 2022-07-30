<?php

namespace App\Controller\Admin;

use App\Entity\Detailcommande;
use App\Form\DetailcommandeType;
use App\Repository\DetailCommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/detailcommande")
 */
class DetailcommandeController extends AbstractController
{
    /**
     * @Route("/", name="app_detailcommande_index", methods={"GET"})
     */
    public function index(DetailCommandeRepository $detailcommandeRepository): Response
    {
        return $this->render('detailcommande/index.html.twig', [
            'detailcommandes' => $detailcommandeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_detailcommande_new", methods={"GET", "POST"})
     */
    public function new(Request $request, DetailCommandeRepository $detailcommandeRepository): Response
    {
        $detailcommande = new Detailcommande();
        $form = $this->createForm(DetailcommandeType::class, $detailcommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $detailcommandeRepository->add($detailcommande, true);

            return $this->redirectToRoute('app_detailcommande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('detailcommande/new.html.twig', [
            'detailcommande' => $detailcommande,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_detailcommande_show", methods={"GET"})
     */
    public function show(Detailcommande $detailcommande): Response
    {
        return $this->render('detailcommande/show.html.twig', [
            'detailcommande' => $detailcommande,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_detailcommande_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Detailcommande $detailcommande, DetailCommandeRepository $detailcommandeRepository): Response
    {
        $form = $this->createForm(DetailcommandeType::class, $detailcommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $detailcommandeRepository->add($detailcommande, true);

            return $this->redirectToRoute('app_detailcommande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('detailcommande/edit.html.twig', [
            'detailcommande' => $detailcommande,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_detailcommande_delete", methods={"POST"})
     */
    public function delete(Request $request, Detailcommande $detailcommande, DetailCommandeRepository $detailcommandeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$detailcommande->getId(), $request->request->get('_token'))) {
            $detailcommandeRepository->remove($detailcommande, true);
        }

        return $this->redirectToRoute('app_detailcommande_index', [], Response::HTTP_SEE_OTHER);
    }
}
