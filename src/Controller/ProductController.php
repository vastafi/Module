<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/products")
 * @method Exception(string $string)
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index")
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return Response
     * @throws Exception
     */
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $category = $request->query->get('category');
        $name = $request->query->get('name');
        $limit = $request->query->get('limit', 8);
        $page = $request->query->get('page', 1);


        $pageNum = $productRepository->countPages($category, $name, $limit);
        if($page <= 0){
            $this->addFlash('warning', "Invalid page number");
            return $this->redirectToRoute('product_index');
        }
        if($limit <= 1){
            $this->addFlash('warning', "Limit can not be negative or zero");
            return $this->redirectToRoute('product_index');
        }
        $products = $productRepository->filter($category, $name, $limit, $page);
        if(!($products) && in_array($page, range(1, $pageNum))){
            throw new BadRequestHttpException("400");
        }
        if($page > $pageNum){
            $this->addFlash('warning', "Invalid page number");
            return $this->redirectToRoute('product_index');
        }
        if ($limit > 100) {
            $this->addFlash('warning', "Limit exceeded");
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/products.html.twig', [
            'products' => $products,


            'currentValues' => [
                'category' => $category,
                'limit' => $limit,
                'page' => $page,
                'name' => $name,
            ],

            'totalPages' => $pageNum
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
            if ($repo->count(['code' => $product->getCode()]) > 0) {
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
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"}, requirements={"id":"\d+"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function edit(Request $request, Product $product): Response
    {
        $product->setUpdatedAt(new \DateTime(null, new \DateTimeZone('Europe/Athens')));
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
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
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

    public function getProductByCode(string $productCode, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['code' => $productCode]);
        {
            if (!$product) {
                throw new NotFoundHttpException('Product not found.');
            }
            return $this->render('product/details.html.twig', ['product' => $product]);
        }
    }
    /**

     * @Route("/contacts", name="contacts")
     * @return Response
     */
    public function contacts(): Response
    {

        return $this->render('contacts.html.twig');

    }
 /**
     * @Route("/about", name="about")
     * @return Response
     */
    public function about(): Response
    {

        return $this->render('about.html.twig');


    }
}
