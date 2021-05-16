<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Form\ProductType;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Response\ApiErrorResponse;

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
    public function index(Request $request)
    {
        $category = $request->query->get('category', null);
        $name = $request->query->get('name', null);
        $limit = $request->query->get('limit', 8);
        $page = $request->query->get('page', 1);
        if($limit > 100){
            return new ApiErrorResponse('1226', 'Search limit cannot exceed 100 items.');
        }
        if($limit <= 0){
            return new ApiErrorResponse('1624', 'Search limit cannot be negative or zero.');
        }
        if($page <= 0){
            return new ApiErrorResponse('1625', 'Page cannot be negative or zero.');
        }
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $totalPages = $repo->countPages($category, $name, $limit);
        if($page > $totalPages){
            return new ApiErrorResponse('1630', 'This page number does not exist.');
        }
        return $this->json(["items"=>$repo->filter($category,
            $name,
            $limit,
            $page), "pagination"=>["limit"=>$limit, "page"=>$page]]);
    }

    /**
     * @Route("/{productCode}", name="api.product.details",requirements={"productCode":"[A][B]\d+"}, methods={"GET"})
     * @param string $productCode
     * @return JsonResponse|Response
     */
    public function getProductByCode(string $productCode)
    {
        $repo=$this->getDoctrine()->getRepository(Product::class);
        $product = $repo->findOneBy(['code'=>$productCode]);
        if(!$product){
            return new Response(null, 404);
        }
        return $this->json($product);
    }

    /**
     * @Route ("/", name="create_prod_api",methods={"POST"})
     * @param Request $request
     * @return JsonResponse|Response
     * @throws Exception
     */
    public function createProduct(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $content = $request->toArray();

        $repo = $this->getDoctrine()->getRepository(Product::class);

        if(isset($content['code']) !== true ){
            return new ApiErrorResponse(400,'Code cant be null!');
        }
        elseif (strlen($content['code']) == 0 ){
            return new ApiErrorResponse(400,'Code cant be blank!');
        }
        elseif(isset($content['name'])==false){
            return new ApiErrorResponse(400,'Name cant be null!');
        }
        elseif (strlen($content['name']) == 0){
            return new ApiErrorResponse(400,'Name cant be blank!');
        }
        elseif(isset($content['price'])==false){
            return new ApiErrorResponse(400,'Price cant be null!');
        }
        elseif (strlen($content['price']) == 0){
            return new ApiErrorResponse(400,'Price cant be blank!');
        }
        elseif(isset($content['category'])==false){
            return new ApiErrorResponse(400,'Category cant be null!');
        }
        elseif (strlen($content['category']) == 0){
            return new ApiErrorResponse(400,'Category cant be blank!');
        }
        elseif(isset($content['availableAmount'])==false){
            return new ApiErrorResponse(400,'Available amount cant be null!');
        }
        elseif (strlen($content['availableAmount']) == 0){
            return new ApiErrorResponse(400,'Available amount cant be blank!');
        }

        $product = new Product();
        $product->setCode($content['code']);
        $product->setName($content['name']);
        $product->setCategory($content['category']);
        $product->setPrice($content['price']);
        $product->setDescription($content['description']);
        $product->setCreatedAt(new DateTime(null, new DateTimeZone('Europe/Athens')));
        $product->setAvailableAmount($content['availableAmount']);

        if ($repo->count(['code'=> $content['code']]) > 0){
            return new ApiErrorResponse(400,'A product with this code exists already!');
        }

        $em->persist($product);

        $em->flush();

        return new Response(null,201);
    }

    /**
     * @Route ("/{productCode}", name="update", requirements={"productCode":"[A][B]\d+"}, methods={"PUT"})
     * @param string $productCode
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function updateProduct(string $productCode, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $product = $repo->findOneBy(['code' => $productCode]);
        if(!$product){
            return new Response(null, 404);
        }
        $product->setUpdatedAt(new DateTime(null, new DateTimeZone('Europe/Athens')));
        $form = $this->createForm(ProductType::class, $product);
        $form->submit($data);
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
        return new Response(null, 200);
    }
    /**
     * @Route("/{productCode}", name="delete_product_api",requirements={"productCode":"[A][B]\d+"}, methods={"DELETE"})
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
