<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter; # remove unused imports

/**
 * @Route("/products")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index")
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $category = $request->query->get('category');
        $name = $request->query->get('name');
        $limit = $request->query->get('limit', 8);
        $page = $request->query->get('page', 1);

        # fixme validation for page, category and name required
        # fixme move validations and fields to a separated object like SearchProductsCriteria
        if ($limit > 100) {
            # @note there is no status code 525
            # @anotherNote I suggest you to use addFlash method and show flashes on frontend.
            return new Response('Search limit cannot exceed 100 items.', 400);
        }


        return $this->render('product/products.html.twig', [
            'products' => $productRepository->filter($category, $name, $limit, $page),

            # @note you should pass categories from database in order to display them on frontend
            'categories' => $productRepository->getCategories(),

            # @note also we need to pass current value for limit and category, so we can use them in url generation
            'currentValues' => [
                'category' => $category,
                'limit' => $limit,
                'name' => $name,
            ],
            // @note amount of pages can be calculated through repo request
            'totalPages' => $productRepository->countPages($category, $name, $limit)
        ]);
    }

    /**
     * @Route("/create", name="product_new", methods={"GET","POST"})
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return Response
     * @throws \Exception
     */
    public function createProduct(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $product->setCreatedAt(new \DateTime(null, new \DateTimeZone('Europe/Athens')));
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($productRepository->count(['code' => $product->getCode()]) > 0) {
                #code 400 bad request
                return $this->render('product/new.html.twig', [
                    'errors' => ['A product with this code exista already!'],
                    'product' => $product,
                    'form' => $form->createView(),
                ], new Response(null, 400));
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

}
