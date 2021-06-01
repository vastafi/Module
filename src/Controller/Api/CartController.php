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
 * @Route("/api/v1/cart", defaults={"_format":"json"})
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="cart_index", methods={"GET"})
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function index(CartRepository $cartRepository)
    {
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
        if(!$product)
        {
            return new Response(null, 404);
        }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $amount = $request->query->get('amount', 1);
        if($product->getAvailableAmount() < $amount){
            return new ApiErrorResponse("1204", "We don't have so many products");
        }
        $product->setAvailableAmount($product->getAvailableAmount() - $amount);
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
        $em->persist($product);
        $em->flush();
        return new Response(null, 200);

    }

    /**
     * @Route("/del/{productCode}", name="cart_remove", methods={"DELETE"})
     * @param $productCode
     * @return Response
     */
    public function remove($productCode): Response
    {
        $em = $this->getDoctrine()->getManager();
        $cartRepository = $this->getDoctrine()->getRepository(Cart::class);
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $product = $productRepository->findOneBy(['code'=>$productCode]);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $cart = $cartRepository->findOneBy(["user"=>$user->getId()]);
        if($cart)
        {
            $product->setAvailableAmount($product->getAvailableAmount() + ($cart->getItems())[array_search($productCode, array_map(function($item){
                    return $item['code'];
                }, $cart->getItems()))]['amount']);
            $cart->removeItem($productCode);
        }
        else {
            return new Response(null, 404);
        }
        $em->persist($cart);
        $em->persist($product);
        $em->flush();
        return new Response(null, 200);
    }

    /**
     * @Route("/update/{productCode}", name="cart_update", methods={"PATCH"})
     * @param Request $request
     * @param string $productCode
     * @return Response
     */
    public function update(Request $request, string $productCode)
    {
        $em = $this->getDoctrine()->getManager();
        $cartRepository = $this->getDoctrine()->getRepository(Cart::class);
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $product = $productRepository->findOneBy(['code'=>$productCode]);
        if(!$product)
        {
            return new Response(null, 404);
        }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $amount = $request->query->get('amount');
        if($product->getAvailableAmount() < $amount){
            return new ApiErrorResponse("1204", "We don't have so many products");
        }
        $user = $this->getUser();
        $cart = $cartRepository->findOneBy(["user"=>$user->getId()]);
        if($cart)
        {
            $product->setAvailableAmount($product->getAvailableAmount() + ($cart->getItems())[array_search($productCode, array_map(function($item){
                    return $item['code'];
                }, $cart->getItems()))]['amount'] - $amount);
            $cart->setAmount($productCode, $amount);
            $cart->setUser($user);
        }
        else{
            return new Response(null, 404);
        }
        $em->persist($product);
        $em->persist($cart);
        $em->flush();
        return new Response(null, 200);
    }
}