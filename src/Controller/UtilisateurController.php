<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Form\UtilisateurTypeModifier;
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
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('backend/utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findBy(array(), array('nomAssociation' => 'ASC')),
        ]);
    }


     #[Route('/recherche', name: 'app_user_search', methods: ['GET','POST'])]
    public function recherche(UtilisateurRepository $utilisateurRepository,Request $request, Environment $twig  ): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $data = $request->get('search');
        $users = $utilisateurRepository->search($data, $offset);

        return new Response($twig->render('backend/utilisateur/index.html.twig', [
             
            
            'utilisateurs' => $users,
           'previous' => $offset - UtilisateurRepository::PAGINATOR_PER_PAGE,
           'next' => min(count($users), $offset + UtilisateurRepository::PAGINATOR_PER_PAGE),
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
        $error ="";
        if ($form->isSubmitted() && $form->isValid() ) {
            $userForm = $form->getData();
            $users = $utilisateurRepository->findByIdentifiant($userForm->getUsername());
            if(count($users)!=0){
                $error = "L'identifiant existe déja !";
            }
            else {

             $utilisateur->setPassword(
                $userPasswordHasher->hashPassword(
                    $utilisateur,
                    $utilisateur->getPassword()
                )
            );
            
           
            $utilisateurRepository->save($utilisateur, true);

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }
        }
         
        
        return $this->renderForm('backend/utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
            'error'=>$error,
            
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
        $form = $this->createForm(UtilisateurTypeModifier::class, $utilisateur);
        $form->handleRequest($request);
        $error="";
        if ($form->isSubmitted() && $form->isValid()) {
            $userForm = $form->getData();
            $users = $utilisateurRepository->findByIdentifiant($userForm->getUsername());
            if(count($users)!=0){
                $error = "L'identifiant existe déja !";
            }
            else {
                $utilisateurRepository->save($utilisateur, true);

                return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
            } 
          
            
           
        }

        return $this->renderForm('backend/utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
            'error'=>$error,
        ]);
    }
    #[Route('/{id}/editmdp', name: 'app_utilisateur_editmdp', methods: ['GET', 'POST'])]
    public function editmdp( SluggerInterface $slugger,Request $request, Utilisateur $utilisateur, UtilisateurRepository $utilisateurRepository,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $error=""  ;
        if($request->isMethod('POST')){
            $password = $request->request->get('p');
            $confirmpassword = $request->request->get('cp');
            
          if (strcmp( $confirmpassword, $password )==0) {
            $utilisateur->setPassword(
                $userPasswordHasher->hashPassword(
                    $utilisateur,
                    $password
                )
            );
        
            $utilisateurRepository->save($utilisateur, true);

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        
        }


            else $error = "Les deux mot de passe doivent etre les mêmes ! ";
          
            
            
        }
    
        return $this->renderForm('backend/utilisateur/editmdp.html.twig', [
            'u' => $utilisateur, 
            'error' => $error,
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
