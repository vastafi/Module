<?php

namespace App\Controller\API;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

    /**
     * @Route("/api/v1/products") - @note can be removed
     */

class ProductController extends AbstractController
{
    /**
     * @Route("/", name="search", methods={"GET"})
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function searchProducts(Request $request)
    {
        if($request->query->get('limit') > 100){
            return new Response('Search limit cannot exceed 100 items.', 525);
        }
        $repo = $this->getDoctrine()->getRepository(Product::class);
        return $this->json($repo->filter($request->query->get('category'),
            $request->query->get('name'),
            $request->query->get('limit'),
            $request->query->get('page')));

    }

    /**
     * @Route("/{productCode}", name="api.product.details",requirements={"productCode":"[A][B]\d+"})
     */
    public function getProductByCode(string $productCode)
    {
        $repo=$this->getDoctrine()->getRepository(Product::class);
        $product = $repo->findOneBy(['code'=>$productCode]);
        if(!$product){
            return new Response('Product not found', 404);
        }
        return $this->json($product);
    }
    /**
     * @Route ("/create", name="create",methods={"POST"})
     */
    public function createProduct(Request $request){
        $product = new Product();
    }
}
