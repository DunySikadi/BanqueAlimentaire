<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccessDeniedHandler extends AbstractController implements AccessDeniedHandlerInterface 
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        // ...

       // return new Response("fuck you ", 403);
      return $this->renderForm('backend/error.html.twig', []);
         
    }
}
