<?php

namespace App\Controller;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Entity\Commande;

use App\Repository\CommandeRepository;
use App\Entity\DetailCommande;
use App\Form\DetailCommandeType;
use App\Repository\DetailCommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;

#[Route('/commande')]
class DetailCommandeController extends AbstractController
{
    
    #[Route('/pdf/{id}', name: 'pdf', methods: ['GET'])]
    public function pdf(Commande $Commande,DetailCommandeRepository $cr): Response
    {
        $html =  $this->renderView('backend/detail_commande/pdf.html.twig', [
            'details' => $cr->findByCommande($Commande),
            
        ]);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
         
        return new Response (
            $dompdf->stream('resume', ["Attachment" => false]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );
        
       
    }
    
    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $cr): Response
    {
        if($this->isGranted('ROLE_ADMIN')){
            return $this->render('backend/detail_commande/listecommande.html.twig', [
                'commandes' => $cr->findAll(),
            ]);
        }
        return $this->render('backend/detail_commande/listecommande.html.twig', [
            'commandes' => $cr->findByUser($this->getUser()),
        ]);
       
    }

    #[Route('/new', name: 'app_detail_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DetailCommandeRepository $detailCommandeRepository , ArticleRepository $articleRepository, CommandeRepository $commandeRepository): Response
    {
        $commande = new Commande();
        $commande->setUtilisateur($this->getUser());
        $commande->setDate(new \DateTime());
        $commande->setCodeCommande("random_bytes(1)");
        $commande->setDate(new \DateTime());
        $commande->setEtat(false);
        
        if($request->isMethod('POST')){
            $commandeRepository->save($commande, true);
            $posts = $request->request->all();
            
            $articles = $request->request->all('article');
            $quantites = $request->request->all('quantite');
            
            foreach($articles as $key => $article){
                $detailCommande = new DetailCommande();

                $detailCommande =$detailCommande->setArticle($articleRepository->find($article));
                $detailCommande =$detailCommande->setQuantite($quantites[$key]) ;
                $detailCommande =$detailCommande->setCommande($commande) ;
                //var_dump($detailCommande->getCommande()->getId());
                $detailCommandeRepository->save($detailCommande, true);
             }
            // $detailCommandeRepository->save($detailCommande, false);
          

           
         }
        return $this->renderForm('backend/detail_commande/new.html.twig', [
            'articles' => $articleRepository->findAll(),
            
            
        ]);



        // if ($form->isSubmitted() && $form->isValid()) {
        //     $detailCommandeRepository->save($detailCommande, true);

        //     return $this->redirectToRoute('app_detail_commande_index', [], Response::HTTP_SEE_OTHER);
        // }
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function show(Commande $Commande,DetailCommandeRepository $dr): Response
    {
        return $this->render('backend/detail_commande/show.html.twig', [
            'details' => $dr->findByCommande($Commande),
            'commande' =>$Commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_detail_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DetailCommande $detailCommande, DetailCommandeRepository $detailCommandeRepository): Response
    {
        $form = $this->createForm(DetailCommandeType::class, $detailCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $detailCommandeRepository->save($detailCommande, true);

            return $this->redirectToRoute('app_detail_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('detail_commande/edit.html.twig', [
            'detail_commande' => $detailCommande,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $Commande,  CommandeRepository $CommandeRepository ,  DetailCommandeRepository $DetailCommandeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$Commande->getId(), $request->request->get('_token'))) {
            $details = $DetailCommandeRepository->findByCommande($Commande);
            foreach($details as $d ){
                $DetailCommandeRepository->remove($d, true);
            }
            $CommandeRepository->remove($Commande, true);
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/accepter', name: 'accepter', methods: ['GET', 'POST'])]
    public function editcommande(Request $request,Commande $Commande, CommandeRepository $CommandeRepository): Response
    {
        

        $Commande->setEtat(true);
            $CommandeRepository->save($Commande, true);

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        

        
    }
}
