<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/products")
 */
class ProductController extends AbstractController
{

    /**
     * @Route("/", name="product_index")
     * @param Request $request
     * @param ProductRepository $productRepository
     */
    public function index(Request $request, ProductRepository $productRepository)
    {
        $category = $request->query->get('category', null);
        $name = $request->query->get('name', null);
        $limit = $request->query->get('limit', 8);
        $page = $request->query->get('page', 1);
        if($limit > 100 || $limit < 2){
            return $this->render('product/products.html.twig', [
                'errors' => ['Invalid limit'],
            ]);
        }
        if($page <= 0){
            return $this->render('product/products.html.twig', [
                'errors' => ['Page number cannot be negative or 0']
            ]);
        }
        $products = $productRepository->filter($category, $name, $limit, $page);
        $totalPages = ceil($productRepository->countProducts($name, $category) / $limit);
        if($productRepository->countProducts($name, $category) == 0){
            return $this->render('product/products.html.twig', [
                'errors' => ['No products found'],
            ]);
        }
        if ($page > $totalPages) {
            return $this->render('product/products.html.twig', [
                'errors' => ['This page number does not exist']
            ]);
        }
        return $this->render('product/products.html.twig', [
            'products' => $products,
            'totalPages' => $totalPages,
            'thisPage' => $page,
            'limit'=>$limit
        ]);
    }

    /**
     * @Route("/create", name="product_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function createProduct(Request $request): Response
    {
        $product = new Product();
        $product->setCreatedAt(new \DateTime(null, new \DateTimeZone('Europe/Athens')));
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repo = $this->getDoctrine()->getRepository(Product::class);
            if ($repo->count(['code'=> $product->getCode()]) > 0){
                #code 400 bad request
                return $this->render('product/new.html.twig', [
                    'errors' => ['A product with this code exista already!'],
                    'product' => $product,
                    'form' => $form->createView(),
                ]);

            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"}, requirements={"id":"\d+"})
     * @param Product $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"}, requirements={"id":"\d+"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"POST"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * @Route("/{productCode}", name="detroduct", requirements={"productCode":"[A][B]\d+"})
     * @param string $productCode
     * @param ProductRepository $productRepository
     * @return Response
     */

    public function getProductByCode(string $productCode,ProductRepository $productRepository): Response
    {
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $product = $productRepository->findOneBy(['code' => $productCode]);
        {
            if (!$product) {
                return $this->render('Exception/errors404.html.twig',['product' => $product]);
            }
            return $this->render('product/details.html.twig', ['product' => $product]);
        }
    }

}
