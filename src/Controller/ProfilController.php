<?php

namespace App\Controller;

use App\Services\Tools;
use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="app_profil")
     */
    public function index(Tools $tools, CommandeRepository $commandeRepository): Response
    {
       
        $user = $tools->getUser();

        $id = $user->getId();

        // dd($id);
      
        // $attente = $commandeRepository->findBy(['user_id'=> $id, 'exped'=> 0]);
        
        // $attente = $commandeRepository->findBy(['user_id'=> $id]);

        // dd($attente);

        $exped = $commandeRepository->findBy(['exped'=> 1]);

       

        return $this->render('profil/index.html.twig', [
            // 'user' => $user,
            // 'attente' => $attente,
            // 'exped' => $exped,
        ]);
    }
}
