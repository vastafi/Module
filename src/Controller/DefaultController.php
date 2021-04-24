<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @note can be deleted, not required */
class DefaultController extends AbstractController
{
    /**
     * @Route("/default", name="default")
     */
    public function show(): Response
    {
        return new Response("Hello world");
    }
}
