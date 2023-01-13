<?php

namespace App\Controller;

use App\Entity\Famille;
use App\Form\FamilleType;
use App\Repository\FamilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route('/admin/famille')]
class FamilleController extends AbstractController
{
    #[Route('/', name: 'app_famille_index', methods: ['GET'])]
    public function  index(Request $request, Environment $twig, FamilleRepository $familleRepository): Response
     {       $offset = max(0, $request->query->getInt('offset', 0));
       $paginator = $familleRepository->getFamillePaginator( $offset);

         return new Response($twig->render('backend/famille/index.html.twig', [
             
            
            'familles' => $paginator,
           'previous' => $offset - FamilleRepository::PAGINATOR_PER_PAGE,
           'next' => min(count($paginator), $offset + FamilleRepository::PAGINATOR_PER_PAGE),
           'PAGINATOR_PER_PAGE' =>   FamilleRepository::PAGINATOR_PER_PAGE ,
         ]));
     }

    #[Route('/new', name: 'app_famille_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FamilleRepository $familleRepository): Response
    {
        $famille = new Famille();
        $form = $this->createForm(FamilleType::class, $famille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $familleRepository->save($famille, true);

            return $this->redirectToRoute('app_famille_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend/famille/new.html.twig', [
            'famille' => $famille,
            'form' => $form,
            'modifier' => false,
        ]);
    }

    #[Route('/{id}', name: 'app_famille_show', methods: ['GET'])]
    public function show(Famille $famille): Response
    {
        return $this->render('backend/famille/show.html.twig', [
            'famille' => $famille,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_famille_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Famille $famille, FamilleRepository $familleRepository): Response
    {
        $form = $this->createForm(FamilleType::class, $famille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $familleRepository->save($famille, true);

            return $this->redirectToRoute('app_famille_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend/famille/new.html.twig', [
            'famille' => $famille,
            'form' => $form,
            'modifier' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_famille_delete', methods: ['POST'])]
    public function delete(Request $request, Famille $famille, FamilleRepository $familleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$famille->getId(), $request->request->get('_token'))) {
            $familleRepository->remove($famille, true);
        }

        return $this->redirectToRoute('app_famille_index', [], Response::HTTP_SEE_OTHER);
    }
}
