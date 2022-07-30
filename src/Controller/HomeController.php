<?php

namespace App\Controller;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Component\Mime\Email;
use App\Repository\ProduitRepository;
use App\Repository\CategoryRepository;
use App\Repository\CarrouselRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(ProduitRepository $ProduitRepository, CarrouselRepository $carrouselRepository, CategoryRepository $categoryRepository): Response
    {
             
        return $this->render('home/index.html.twig', [
            'produits' => $ProduitRepository->findAll(), 
            // 'carrousel' => $carrouselRepository->findAll(),       
            'carrousel' => $carrouselRepository->findBy(['status'=> true]),            
            'categorys' => $categoryRepository->findAll(),            
        ]); 
           
    }


    
    /**
     * @Route("/detail-produit/{id}", name="app_detail_produit")
     */
    public function detail($id,ProduitRepository $ProduitRepository): Response
    // on peut aussi faire : public function detail(Produit $produit): Response

    {
             
        return $this->render('home/show.html.twig', [
            'produit' => $ProduitRepository->find($id)    
            
            // et aller chercher : 'produit' => $produit
        ]); 
           
    }

     /**
     * @Route("/filtre-produit/{id}", name="app_filtre_produit")
     */
    public function filtre($id,ProduitRepository $ProduitRepository, CategoryRepository $categoryRepository): Response
  
    {
        $produits = $ProduitRepository->findBy(['category' => $id]);     

        return $this->render('home/filtre.html.twig', [
            'produits' => $produits,  
            'categorys' => $categoryRepository->findAll(),   

            
            // et aller chercher : 'produit' => $produit
        ]); 
           
    }


    /**
     * @Route("/test-mail", name="app_test_mail")
     */
    public function sendEmail(MailerInterface $mailer): Response
    {
        $mj = new Client('79432f7490111776b075c33c08c48c3b','9b6546207fea1969e4ce090b7522c1cd',true,['version' => 'v3.1']);
    $body = [
      'Messages' => [
        [
          'From' => [
            'Email' => "jmb-dw26@outlook.fr",
            'Name' => "Phone"
          ],
          'To' => [
            [
              'Email' => "jmb22.dw@gmail.com",
              'Name' => "JMB"
            ]
          ],
          'Subject' => "Greetings from Mailjet.",
          'TextPart' => "My first Mailjet email",
          'HTMLPart' => "<h3>Dear passenger 1, welcome to <a href='https://www.mailjet.com/'>Mailjet</a>!</h3><br />May the delivery force be with you!",
          'CustomID' => "AppGettingStartedTest"
        ]
      ]
    ];
    $response = $mj->post(Resources::$Email, ['body' => $body]);
    $response->success() && var_dump($response->getData());
    return $this->redirectToRoute('app_home');


        // dd($mailer);

        // return $this->redirectToRoute('app_home');
    }

}
