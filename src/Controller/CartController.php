<?php


namespace App\Controller;


use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\CheckoutType;
use App\Form\OrderEditType;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Response\ApiErrorResponse;
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
     * @Route("/", name="cart")
     * @return Response
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('cart/cart.html.twig');
    }

    /**
     * @Route("/checkout", name="checkout", methods={"GET","POST"})
     */
    public function checkout(Request $request):Response{
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $em = $this->getDoctrine()->getManager();
        $cartRepository = $this->getDoctrine()->getRepository(Cart::class);
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $user = $this->getUser();
        $cart = $cartRepository->findOneBy(["user"=>$user->getId()]);
        $items = [];
        $total = 0;
        if($cart) {
            $products = $productRepository->findBy(['code' => array_column($cart->getItems(), 'code')]);
            foreach ($products as $product) {
                $amount = array_column($cart->getItems(), 'amount', 'code')[$product->getCode()];
                if ($product->getAvailableAmount() < $amount) {
                    return new ApiErrorResponse('14068', 'We don\'t have such an amount for ' . $product->getName());
                }

                $items[] = ['code' => $product->getCode(),
                    'amount' => $amount,
                    'price' => $product->getPrice()];
                $total += $amount * $product->getPrice();
            }
        }
        $order = $this->createOrder($items, $total);
        $form = $this->createForm(CheckoutType::class, $order);
        $form->handleRequest($request);
        dump($order);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setAvailableAmount($product->getAvailableAmount() - $amount);
            $em->persist($product);
            $cartRepository->removeCart($cart->getId());
            $em->persist($order);
            $em->flush();

            $this->addFlash('order_placed', 'Your order has been submitted! You can view it in your cabinet.');
            return $this->redirectToRoute('product_index');
        }
        $em->flush();

//            return $this->redirectToRoute('checkout_form',['id' => $order->getId()]);
        return $this->render('order/checkout.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
        //return new Response(null, 404);
    }

    public function createOrder(array $items, float $total):Order{
        $order = new Order();
        $order->setItems($items);
        $order->setStatus('New');
        $order->setTotal($total);
        $order->setUser($this->getUser());
        return $order;
    }
//    /**
//     * @Route("checkout/{id}", name="checkout_form", methods={"GET","POST"})
//     */
//    public function checkout(Request $request, Order $order): Response
//    {
//        $form = $this->createForm(CheckoutType::class, $order);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $this->getDoctrine()->getManager()->flush();
//
//            return $this->redirectToRoute('product_index');
//        }
//
//        return $this->render('order/checkout.html.twig', [
//            'order' => $order,
//            'form' => $form->createView(),
//        ]);
//    }
}