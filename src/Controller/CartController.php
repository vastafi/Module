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
     * @param Request $request
     * @param CartRepository $cartRepository
     * @return JsonResponse|Response
     */
    public function index(Request $request, CartRepository $cartRepository, ProductRepository $productRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $items = $cartRepository->findOneBy(["user"=>$this->getUser()->getId()])->getItems();
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

        return $this->render('cart/fragment.html.twig', [
            "items" => $cartWithData,
            "total" => $total
        ]);

    }
}