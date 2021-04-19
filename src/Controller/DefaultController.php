<?php


namespace App\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/proba", name="default")
     */
    public function show():Response
    {
//        return $this->render('admin/products.html.twig');
        return $this->render('admin/products.html.twig');
    }
}
