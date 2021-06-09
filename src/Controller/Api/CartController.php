<?php


namespace App\Controller\Api;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Response\ApiErrorResponse;
use Doctrine\ORM\EntityManagerInterface;
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
     */
    public function index(CartRepository $cartRepository): Response
    {
        return $this->json($cartRepository->findOneBy(["user"=>$this->getUser()->getId()])->getItems());
    }

    /**
     * @Route("/add/{productCode}", name="cart_add", methods={"POST"})
     */
    public function add(
        Request $request,
        string $productCode,
        CartRepository $cartRepository,
        ProductRepository $productRepository,
        EntityManagerInterface $em
    ): Response
    {
        $product = $productRepository->findOneBy(['code'=>$productCode]);
        if(!$product)
        {
            return new Response(null, 404);
        }
        $amount = $request->query->get('amount', 1);
        if ($product->getAvailableAmount() < $amount) {
            return new ApiErrorResponse("1204", "We don't have so many products");
        }
        $product->setAvailableAmount($product->getAvailableAmount() - $amount);
        $user = $this->getUser();
        $cart = $cartRepository->findOneBy(["user" => $user->getId()]);
        if (!$cart) {
            $cart = new Cart();
        }
        $cart->addItem($productCode, $amount);
        $cart->setUser($user);

        $em->persist($cart);
        $em->persist($product);
        $em->flush();
        return new Response(null, 200);

    }

    /**
     * @Route("/del/{productCode}", name="cart_remove", methods={"DELETE"})
     */
    public function remove(
        $productCode,
        CartRepository $cartRepository,
        ProductRepository $productRepository,
        EntityManagerInterface $em
    ): Response
    {
        $product = $productRepository->findOneBy(['code'=>$productCode]);
        $user = $this->getUser();
        $cart = $cartRepository->findOneBy(["user"=>$user->getId()]);
        if (!$cart) {
            return new Response(null, 404);
        }
        $cart->removeItem($productCode);
        $em->persist($cart);
        $em->flush();
        return new Response(null, 200);
    }

    /**
     * @Route("/update/{productCode}", name="cart_update", methods={"PATCH"})
     */
    public function update(
        Request $request,
        CartRepository $cartRepository,
        ProductRepository $productRepository,
        EntityManagerInterface $em,
        string $productCode
    ): Response
    {
        $product = $productRepository->findOneBy(['code'=>$productCode]);
        if(!$product) {
            return new Response(null, 404);
        }
        $amount = $request->query->get('amount');
        if($product->getAvailableAmount() < $amount){
            return new ApiErrorResponse("1204", "We don't have so many products");
        }
        $user = $this->getUser();
        $cart = $cartRepository->findOneBy(["user"=>$user->getId()]);

        if (!$cart) {
            return new Response(null, 404);
        }
        $cart->setAmount($productCode, $amount);
        $cart->setUser($user);
        $em->persist($cart);
        $em->flush();
        return new Response(null, 200);
    }
}