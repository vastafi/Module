<?php


namespace App\Controller\Api;

use App\Entity\Cart;
use App\Repository\CartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/cart")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="cart_index", methods={"GET"})
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function index(Request $request, CartRepository $cartRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->json($cartRepository->findOneBy(["user"=>$this->getUser()->getId()])->getItems());
    }

    /**
     * @Route("/add/{productCode}", name="cart_add", methods={"POST"})
     * @param Request $request
     * @param string $productCode
     * @return Response
     */
    public function add(Request $request, string $productCode)
    {
        $em = $this->getDoctrine()->getManager();
        $cartRepository = $this->getDoctrine()->getRepository(Cart::class);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $amount = $request->query->get('amount', 1);
        $user = $this->getUser();
        $cart = $cartRepository->findOneBy(["user"=>$user->getId()]);
        if($cart)
        {
            $item = ["code"=>$productCode, "amount"=>$amount];
            $items = $cart->getItems();
            array_push($items, $item);
            $cart->setItems($items);
            $cart->setUser($user);
        }
        elseif (!$cart)
        {
            $cart = new Cart();
            $cart->setItems([["code"=>$productCode, "amount"=>$amount]]);
            $cart->setUser($user);
        }
        $em->persist($cart);
        $em->flush();
        return new Response(null, 200);

    }

    /**
     * @Route("/del/{productCode}", name="cart_remove")
     * @param $productCode
     * @return Response
     */
    public function remove($productCode): Response
    {
        $em = $this->getDoctrine()->getManager();
        $cartRepository = $this->getDoctrine()->getRepository(Cart::class);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $cart = $cartRepository->findOneBy(["user"=>$user->getId()]);
        if($cart)
        {
            $items = $cart->getItems();
            unset($items[array_search($productCode, array_map(function($item) {
                return $item['code'];
            }, $items))]);
            $cart->setItems($items);
        }
        $em->persist($cart);
        $em->flush();
        return new Response(null, 200);
    }
}