<?php

namespace App\Controller;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Entity\Commande;
use App\Repository\ClassificationRepository;
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
    #[Route('/csv/{id}', name: 'csv', methods: ['GET'])]
    public function csv(Commande $Commande,DetailCommandeRepository $cr): Response
    {
        $myVariableCSV = "Type;Ref Cde;Client;Ste-01;B.A;DateLivraison;Date Commande;Commentaire Commande\n";
    //Adding data (with the . in front to add the data to the existing variable)
   
    //If you wish to add a space
    $myVariableCSV .= "E;".$Commande->getId().";".$Commande->getUtilisateur()->getNomAssociation().";01;87;???;".$Commande->getDate()->format('Y-m-d H:i:s').";".$Commande->getCommentaire()."\n";
    //Other data
    $myVariableCSV .= "\n";
    $myVariableCSV .= " ;No ligne; Article; Qte Commandee; Unite Poids; Commentaire ligne\n";
    $details = $cr->findByCommande($Commande);
    foreach($details as $key => $value ){
        $myVariableCSV .= "L;".strval($key+1).";".$value->getArticle()->getCodeArticle().";".$value->getQuantite()."; Kg ;  \n";

    }
    //We give the variable in string to the response, we set the HTTP code to 200
    return new Response(
           $myVariableCSV,
           200,
           [
         //Defines the content of the query as an Excel file
             'Content-Type' => 'application/vnd.ms-excel',
         //We indicate that the file will be in attachment so opening the download box, as well as the definition of the name of the file
             "Content-disposition" => "attachment; filename=commande.csv"
          ]
    );
        
       
    }
    
    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $cr ): Response
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

    #[Route('/recherche', name: 'app_commande_search', methods: ['GET','POST'])]
    public function recherche(CommandeRepository $cr,Request $request  ): Response
    {
        $data = $request->get('search');
        $cmd = $cr->search($data);

        return $this->render('backend/detail_commande/listecommande.html.twig', [
            'commandes' => $cmd,
             
        ]);
    }
    function dateDiffInDays($date1, $date2) 
  {
       
      $diff = strtotime($date2) - strtotime($date1);
      return  round($diff / 86400);
  }
    #[Route('/new', name: 'app_detail_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request,ClassificationRepository $classificationRepo ,  DetailCommandeRepository $detailCommandeRepository , ArticleRepository $articleRepository, CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();
        $erreur=" ";
        $arts = $articleRepository->afficher();
        $classifications = $user->getClassifications();
       foreach($arts as $key => $value){
         $test = false;
        foreach($classifications as $c){
            if($value->getClassification()->getId() == $c->getId() )  $test = true;    
        }
        if(!$test)  unset($arts[$key]);
         
       }


        $commande = new Commande();
        $commande->setUtilisateur($this->getUser());
        $today = new \DateTime();
        $commande->setDate($today);
        $commande->setCodeCommande("random_bytes(1)");
        $commande->setDate(new \DateTime());
        $commande->setEtat(false);
        
         
        if($request->isMethod('POST')){
            $commentaire = $request->request->get('commentaire');
            $date = $request->request->get('date');
            $beneficiaire = $request->request->get('beneficiaire');
            $commande->setDateSouhaite(new \DateTime($date));
            $commande->setBeneficiaire($beneficiaire);
            $commande->setCommentaire($commentaire);
            if($this->dateDiffInDays($today->format('Y-m-d H:i:s') ,$date)>=2){
                $commandeRepository->save($commande, true);
                $posts = $request->request->all();
            
            $articles = $request->request->all('article');
            $quantites = $request->request->all('quantite');
            
            
            foreach($articles as $key => $article){
                $detailCommande = new DetailCommande();

                $detailCommande =$detailCommande->setArticle($articleRepository->find($article));
                $detailCommande =$detailCommande->setQuantite($quantites[$key]) ;
                $detailCommande =$detailCommande->setCommande($commande) ;
                 
                $detailCommandeRepository->save($detailCommande, true);

                return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
             }
            }
            else $erreur.=" ! la date souhaité doit etre au minimum aprés 2 jour de la date de commande ! ";
            
            
          

           
         }
        return $this->renderForm('backend/detail_commande/new.html.twig', [
            'articles' => $arts,
            'erreur' => $erreur,
            
            
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
