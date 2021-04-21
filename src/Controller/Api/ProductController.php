<?php

namespace App\Controller\API;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

    /**
     * @Route("/api/v1/products")
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
     * @Route("/{productCode}", name="product.details",requirements={"productCode":"[A][B]\d+"},methods={"GET"})
     * @param string $productCode
     * @return JsonResponse|Response
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
//
    /**
     * @Route ("/", name="create",methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function createProduct(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $content = $request->toArray();

        $repo = $this->getDoctrine()->getRepository(Product::class);

        $product = new Product();
        $product->setCode($content['code']);
        $product->setName($content['name']);
        $product->setCategory($content['category']);
        $product->setPrice($content['price']);
        $product->setDescription($content['description']);
        $product->setCreatedAt(new \DateTime(null, new \DateTimeZone('Europe/Athens')));

        if ($repo->count(['code'=> $product->getCode()]) > 0){
            return new Response('A product with this code exists already!',400);
        }
        elseif (strlen($content['code']) == 0){
            return new Response('Code cant be blank!',400);
        }
        elseif (strlen($content['name']) == 0){
            return new Response('Name cant be blank!',400);
        }
        elseif (strlen($content['price']) == 0){
            return new Response('Price cant be blank!',400);
        }
        elseif (strlen($content['category']) == 0){
            return new Response('Category cant be blank!',400);
        }

        $em->persist($product);

        $em->flush();

        return new Response(null ,201);
    }
    /**
     * @Route("/{productCode}", name="delete",requirements={"productCode":"[A][B]\d+"}, methods={"DELETE"})
     * @param string $productCode
     * @return Response
     */
    public function deleteProductByCode(string $productCode):Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $product = $repo->findOneBy(['code' => $productCode]);
        if(!$product){
            return new Response(null, 404);
        }

        $entityManager->remove($product);
        $entityManager->flush();

        return new Response();
    }

}
