<?php

namespace App\Controller;

use App\Entity\SousFamille;
use App\Form\SousFamilleType;
use App\Repository\SousFamilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/sous/famille')]
class SousFamilleController extends AbstractController
{
    #[Route('/', name: 'app_sous_famille_index', methods: ['GET'])]
    public function index(SousFamilleRepository $sousFamilleRepository): Response
    {
        return $this->render('backend/sous_famille/index.html.twig', [
            'sous_familles' => $sousFamilleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_sous_famille_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SousFamilleRepository $sousFamilleRepository): Response
    {
        $sousFamille = new SousFamille();
        $form = $this->createForm(SousFamilleType::class, $sousFamille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sousFamilleRepository->save($sousFamille, true);

            return $this->redirectToRoute('app_sous_famille_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend/sous_famille/new.html.twig', [
            'sous_famille' => $sousFamille,
            'form' => $form,
            'modifier' => false,
        ]);
    }

    #[Route('/{id}', name: 'app_sous_famille_show', methods: ['GET'])]
    public function show(SousFamille $sousFamille): Response
    {
        return $this->render('backend/sous_famille/show.html.twig', [
            'sous_famille' => $sousFamille,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sous_famille_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SousFamille $sousFamille, SousFamilleRepository $sousFamilleRepository): Response
    {
        $form = $this->createForm(SousFamilleType::class, $sousFamille);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sousFamilleRepository->save($sousFamille, true);

            return $this->redirectToRoute('app_sous_famille_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend/sous_famille/new.html.twig', [
            'sous_famille' => $sousFamille,
            'form' => $form,
            'modifier' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_sous_famille_delete', methods: ['POST'])]
    public function delete(Request $request, SousFamille $sousFamille, SousFamilleRepository $sousFamilleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sousFamille->getId(), $request->request->get('_token'))) {
            $sousFamilleRepository->remove($sousFamille, true);
        }

        return $this->redirectToRoute('app_sous_famille_index', [], Response::HTTP_SEE_OTHER);
    }
}
