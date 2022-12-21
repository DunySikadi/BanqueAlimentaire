<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use App\Repository\ClassificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/utilisateur')]
class UtilisateurController extends AbstractController
{
       
    #[Route('/', name: 'app_utilisateur_index', methods: ['GET'])]
    public function  index(Request $request, Environment $twig,UtilisateurRepository $utilisateurRepository): Response
     {       $offset = max(0, $request->query->getInt('offset', 0));
       $paginator = $utilisateurRepository->getUserPaginator( $offset);

         return new Response($twig->render('backend/utilisateur/index.html.twig', [
             
            
            'utilisateurs' => $paginator,
           'previous' => $offset - UtilisateurRepository::PAGINATOR_PER_PAGE,
           'next' => min(count($paginator), $offset + UtilisateurRepository::PAGINATOR_PER_PAGE),
         ]));
     }
    
 /*   #[Route('/', name: 'app_utilisateur_index', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository ): Response
    {
        return $this->render('backend/utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }*/

    #[Route('/new', name: 'app_utilisateur_new', methods: ['GET', 'POST'])]
    public function new(UserPasswordHasherInterface $userPasswordHasher,Request $request, UtilisateurRepository $utilisateurRepository,ClassificationRepository $classificationrepo, SluggerInterface $slugger): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            var_dump("yes");
            $utilisateur->setPassword(
                $userPasswordHasher->hashPassword(
                    $utilisateur,
                    $utilisateur->getPassword()
                )
            );
            
           
            $utilisateurRepository->save($utilisateur, true);

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }
         
        
        return $this->renderForm('backend/utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
            
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateur_show', methods: ['GET'])]
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit( SluggerInterface $slugger,Request $request, Utilisateur $utilisateur, UtilisateurRepository $utilisateurRepository,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->setPassword(
                $userPasswordHasher->hashPassword(
                    $utilisateur,
                    $utilisateur->getPassword()
                )
            );
          
            
            $utilisateurRepository->save($utilisateur, true);

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend/utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateur_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateur $utilisateur, UtilisateurRepository $utilisateurRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $utilisateurRepository->remove($utilisateur, true);
        }

        return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }
}
