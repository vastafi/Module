<?php


namespace App\Session;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartSession
{
//    protected $session;
//    protected $productRepository;
//
//    public function __construct(SessionInterface $session, ProductRepository $productRepository)
//    {
//        $this->session = $session;
//        $this->productRepository = $productRepository;
//    }
//
//    public function add(string $code)
//    {
//        $carts = $this->session->get('carts', []);
//
//        if (empty($carts[$code])) {
//            $carts[$code] = 0;
//        }
//
//        $carts[$code]++;
//        $this->session->set('carts', $carts);
//    }
//
//    public function remove(string $code)
//    {
//        $carts = $this->session->get('carts', []);
//
//        if (!empty($carts[$code])) {
//            unset($carts[$code]);
//        }
//
//        $this->session->set('carts', $carts);
//    }
//
//    public function getFullCart(): array
//    {
//        $carts = $this->session->get('carts', []);
//
//        $cartsWithData = [];
//
//        foreach ($carts as $code => $amount) {
//            $cartsWithData[] = [
//                'product' => $this->productRepository->find($code)->getCode(),
//                'amount' => $amount
//            ];
//        }
//
//        return $cartsWithData;
//    }
//
//    public function getTotal()
//    {
//        $cartsWithData = $this->getFullCart();
//
//        $total = 0;
//
//        foreach ($cartsWithData as $couple) {
//            $total += $couple['product']->getPrice() * $couple['amount'];
//        }
//
//        return $total;
//    }
}