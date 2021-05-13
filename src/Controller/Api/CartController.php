<?php


namespace App\Controller\Api;

use App\Entity\Cart;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Response\ApiErrorResponse;
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
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $product = $productRepository->findOneBy(['code'=>$productCode]);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $amount = $request->query->get('amount', 1);
        if($product->getAvailableAmount() < $amount){
            return new ApiErrorResponse("1204", "We don't have so many products");
        }
        $user = $this->getUser();
        $cart = $cartRepository->findOneBy(["user"=>$user->getId()]);
        if($cart)
        {
            $cart->addItem($productCode, $amount);
            $cart->setUser($user);
        }
        else
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
            $cart->removeItem($productCode);
        }
        else {
            return new Response(null, 404);
        }
        $em->persist($cart);
        $em->flush();
        return new Response(null, 200);
    }
}