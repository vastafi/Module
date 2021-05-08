<?php

namespace App\Controller\Api;

use App\Repository\ProductRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("api/v1/cart")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="cart_index", methods={"GET"})
     * @param SessionInterface $session
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(SessionInterface $session, ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $carts = $session->get('carts', []);

        $cartsWithData = [];

        foreach ($carts as $productCode => $amount) {
            $product = $productRepository->findOneBy(["code" => $productCode]);
            if ($product->getAvailableAmount() < $amount) {
                $this->addFlash('warning', 'We don\'t have so many products');
                $amount = $product->getAvailableAmount();
            }
            $cartsWithData[] = [
                'product' => $product,
                'amount' => $amount
            ];
            $carts[$productCode] = $amount;
        }

        $total = 0;

        foreach ($cartsWithData as $couple) {
            $total += $couple['product']->getPrice() * $couple['amount'];
        }
        $session->set('carts', $carts);

        return $this->render('cart/index.html.twig', [
            "items" => $cartsWithData,
            "total" => $total
        ]);
    }

    /**
     * @Route("/add/{productCode}", name="cart_add")
     * @param $productCode
     * @param SessionInterface $session
     * @return RedirectResponse
     */
    public function add($productCode, SessionInterface $session): RedirectResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $carts = $session->get('carts', []);

        if (empty($cards[$productCode])) {
            $carts[$productCode] = '';
        }

        $carts[$productCode]++;

        $session->set('carts', $carts);

        return $this->redirectToRoute("detroduct", ["productCode" => $productCode]);
    }

    /**
     * @Route("/update/{productCode}", name="cart_update", methods={"POST"})
     * @param $productCode
     * @param SessionInterface $session
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return RedirectResponse
     */
    public function update($productCode, SessionInterface $session, Request $request, ProductRepository $productRepository): RedirectResponse
    {
        $carts = $session->get('carts', []);

        if (!empty($carts[$productCode])) {
            if ($request->request->get('add') === "true") {
                $carts[$productCode]++;
            } elseif ($request->request->get('add') === "false") {
                $carts[$productCode]--;
            }
        }

        $session->set('carts', $carts);

        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/del/{productCode}", name="cart_remove")
     * @param $productCode
     * @param SessionInterface $session
     * @return RedirectResponse
     */
    public function remove($productCode, SessionInterface $session): RedirectResponse
    {
        $carts = $session->get('carts', []);

        if (!empty($carts[$productCode])) {
            unset($carts[$productCode]);
        }

        $session->set('carts', $carts);

        return $this->redirectToRoute('cart_index');
    }
}

