<?php

namespace App\Controller\Admin;

use App\Entity\Photos;
use App\Form\PhotosType;
use App\Repository\PhotosRepository;
use App\Repository\ProduitRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/photos")
 */
class PhotosController extends AbstractController
{
    /**
     * @Route("/", name="app_photos_index", methods={"GET"})
     */
    public function index(PhotosRepository $photosRepository): Response
    {
        return $this->render('photos/index.html.twig', [
            'photos' => $photosRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="app_photos_new", methods={"GET", "POST"})
     */
    public function new($id, Request $request, PhotosRepository $photosRepository,ProduitRepository $produitRepository): Response
    {
        $produit = $produitRepository->find($id);
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

            // return $this->redirectToRoute('app_photos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('photos/new.html.twig', [
            'photo' => $photo,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_photos_show", methods={"GET"})
     */
    public function show(Photos $photo): Response
    {
        return $this->render('photos/show.html.twig', [
            'photo' => $photo,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_photos_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Photos $photo, PhotosRepository $photosRepository): Response
    {
        $form = $this->createForm(PhotosType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photosRepository->add($photo, true);

            return $this->redirectToRoute('app_photos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('photos/edit.html.twig', [
            'photo' => $photo,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_photos_delete", methods={"POST"})
     */
    public function delete(Request $request, Photos $photo, PhotosRepository $photosRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$photo->getId(), $request->request->get('_token'))) {
                 
            $photosRepository->remove($photo, true);

            
            // $filename = $photo->getName();            
            // $fileSystem = new Filesystem();            
            // $projectDir = $this->getParameter('kernel.project_dir');
            // $fileSystem->remove($projectDir.'/public/images/'.$filename);

            $path = $this->getParameter('upload_dir') . $photo->getName();
            // dd($path);
            unlink($path);
        }

        

        return $this->redirectToRoute('app_produit_show', ['id' => $photo->getProduit()->getId()] , Response::HTTP_SEE_OTHER);
    }
}
