<?php

namespace App\Controller;


use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('cart/cart.html.twig', [
            'controller_name' => 'CartController',
        ]);
    }
}