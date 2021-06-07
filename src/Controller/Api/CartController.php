<?php


namespace App\Controller\Api;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\CheckoutType;
use App\Form\OrderType;
use App\Repository\CartRepository;
use App\Response\ApiErrorResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/api/v1/cart")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="cart_index", methods={"GET"})
     * @param CartRepository $cartRepository
     * @return JsonResponse|Response
     */
    public function index(CartRepository $cartRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $cart = $cartRepository->findOneBy(["user"=>$this->getUser()->getId()]);
        if($cart){
            $productRepository = $this->getDoctrine()->getRepository(Product::class);
            $products = $productRepository->findBy(['code' => array_column($cart->getItems(), 'code')]);
            $cartItem = [];
            foreach ($products as $product){
                $cartItem [] = [
                    'product' => $product,
                    'amount' => array_column($cart->getItems(), 'amount', 'code')[$product->getCode()]
                ];
            }
            return $this->json($cartItem);
        }
        else{
            return new Response(null, 404);
        }
    }

    /**
     * @Route("/{productCode}", name="cart_add", methods={"POST"})
     * @param Request $request
     * @param string $productCode
     * @return Response
     */
    public function add(Request $request, string $productCode)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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
     * @Route("/{productCode}", name="cart_remove", methods={"DELETE"})
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

    /**
     * @Route("/", name="cart_update", methods={"PATCH"})
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cartRepository = $this->getDoctrine()->getRepository(Cart::class);
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $productCode = $request->query->get('code');
        $product = $productRepository->findOneBy(['code'=>$productCode]);
        if(!$product)
        {
            return new Response(null, 404);
        }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $amount = $request->query->get('amount');
        $user = $this->getUser();
        $cart = $cartRepository->findOneBy(["user"=>$user->getId()]);
        if($cart)
        {
            $stock = $product->getAvailableAmount() + array_column($cart->getItems(), 'amount', 'code')[$productCode];
            if($stock < $amount){
                return new ApiErrorResponse("1204", "We don't have so many products");
            }
            $cart->setAmount($productCode, $amount);
            $cart->setUser($user);
        }
        else{
            return new Response(null, 404);
        }
        $em->persist($cart);
        $em->flush();
        return new Response(null, 200);
    }

//    /**
//     * @Route("/checkout", name="checkout", methods={"GET","POST"})
//     */
//    public function checkout(Request $request):Response{
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        $em = $this->getDoctrine()->getManager();
//        $cartRepository = $this->getDoctrine()->getRepository(Cart::class);
//        $productRepository = $this->getDoctrine()->getRepository(Product::class);
//        $user = $this->getUser();
//        $cart = $cartRepository->findOneBy(["user"=>$user->getId()]);
//        $items = [];
//        $total = 0;
//        if($cart) {
//            $products = $productRepository->findBy(['code' => array_column($cart->getItems(), 'code')]);
//            foreach ($products as $product) {
//                $amount = array_column($cart->getItems(), 'amount', 'code')[$product->getCode()];
//                if ($product->getAvailableAmount() < $amount) {
//                    return new ApiErrorResponse('14068', 'We don\'t have such an amount for ' . $product->getName());
//                }
//
//                $items[] = ['code' => $product->getCode(),
//                    'amount' => $amount,
//                    'price' => $product->getPrice()];
//                $total += $amount * $product->getPrice();
//            }
//        }
//            $order = $this->createOrder($items, $total);
//            $form = $this->createForm(CheckoutType::class, $order);
//            $form->handleRequest($request);
//            dump($order);
//
//            if ($form->isSubmitted() && $form->isValid()) {
//                $product->setAvailableAmount($product->getAvailableAmount() - $amount);
//                $em->persist($product);
//                $cartRepository->removeCart($cart->getId());
//                $em->persist($order);
//                $em->flush();
//
//                return $this->redirectToRoute('product_index');
//            }
//            $em->flush();
//
////            return $this->redirectToRoute('checkout_form',['id' => $order->getId()]);
//        return $this->render('order/checkout.html.twig', [
//            'order' => $order,
//            'form' => $form->createView(),
//        ]);
//        //return new Response(null, 404);
//    }
//
//    public function createOrder(array $items, float $total):Order{
//        $order = new Order();
//        $order->setItems($items);
//        $order->setStatus('New');
//        $order->setTotal($total);
//        $order->setUser($this->getUser());
//        return $order;
//    }
#fixme create form with the existing data after checkout and redirect to it
//    public function checkout(Request $request):Response{
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
//        $em = $this->getDoctrine()->getManager();
//        $cartRepository = $this->getDoctrine()->getRepository(Cart::class);
//        $productRepository = $this->getDoctrine()->getRepository(Product::class);
//        $user = $this->getUser();
//        $cart = $cartRepository->findOneBy(["user"=>$user->getId()]);
//        $items = [];
//        $total = 0;
//        if($cart){
//            $products = $productRepository->findBy(['code' => array_column($cart->getItems(), 'code')]);
//            foreach($products as $product){
//                $amount = array_column($cart->getItems(), 'amount', 'code')[$product->getCode()];
//                if($product->getAvailableAmount() < $amount){
//                    return new ApiErrorResponse('14068', 'We don\'t have such an amount for '.$product->getName());
//                }
//                $product->setAvailableAmount($product->getAvailableAmount() - $amount);
//                $em->persist($product);
//                $items[] = ['code'=>$product->getCode(),
//                    'amount'=>$amount,
//                    'price'=>$product->getPrice()];
//                $total += $amount * $product->getPrice();
//            }
//            $order = $this->createOrder($items, $total);
//            $form = $this->createForm(CheckoutType::class, $order);
//            $form->handleRequest($request);
//            $cartRepository->removeCart($cart->getId());
//
//            if($form->isSubmitted() && $form->isValid()){
//                $em->persist($order);
//                $em->flush();
//                return new Response(null, 200);
//            }
//            $em->flush();
//            return $this->render('order/new.html.twig', [
//                'order' => $order,
//                'form' => $form->createView(),
//            ]);
//        }
//        return new Response(null, 404);
//    }

}

