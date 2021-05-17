<?php


namespace App\Controller;


use App\Entity\Cart;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="cart", methods={"GET"})
     * @param CartRepository $cartRepository
     * @param ProductRepository $productRepository
     * @return JsonResponse|Response
     */
    public function index(CartRepository $cartRepository, ProductRepository $productRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $cart = $cartRepository->findOneBy(["user"=>$this->getUser()->getId()]);
        if($cart){
            $items = $cart->getItems();
            $cartWithData = [];
            foreach ($items as $id=>$item){
                $product = $productRepository->findOneBy(["code"=>$item['code']]);
                $cartWithData[] = [
                    'product' => $product,
                    'amount' => $item['amount']
                ];
                $carts[$item['code']] = $item['amount'];

            }
            $total = 0;

            foreach ($cartWithData as $couple) {
                $total += $couple['product']->getPrice() * $couple['amount'];
            }
            return $this->render('cart/cart.html.twig', [
                "items" => $cartWithData,
                "total" => $total
            ]);
        }
        else{
            $items = null;
            return $this->render('cart/cart.html.twig',[
                "items"=>$items
            ]);
        }
    }
}