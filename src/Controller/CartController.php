<?php


namespace App\Controller;


use App\Entity\Cart;
use App\Entity\Order;
use App\Form\CheckoutType;
use App\Form\OrderEditType;
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
     * @Route("/", name="cart")
     * @return Response
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('cart/cart.html.twig');
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