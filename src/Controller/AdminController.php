<?php


namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{

    /**
     * @Route("/products", name="adminpr")
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function indexAdmin(Request $request, ProductRepository $productRepository): Response
    {
        $category=$request->query->get('category',null);
        $name=$request->query->get('name',null);
        $limit=$request->query->get('limit',100);
        $page=$request->query->get('page',1);
        $products = $productRepository->filter($category,$name,$limit,$page);

        $totalPages = count($products);
        return $this->render('admin/products.html.twig', [
            'products' => $products,
            'totalPages'=>$totalPages
        ]);
    }
    /**
     * @Route("/products/{id}", name="show", methods={"GET"}, requirements={"id":"\d+"})
     * @param Product $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        return $this->render('admin/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/products/{id}/edit", name="edit", methods={"GET","POST"}, requirements={"id":"\d+"})
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

            return $this->redirectToRoute('adminpr');
        }

        return $this->render('admin/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/products/{id}", name="delete", methods={"POST"})
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

        return $this->redirectToRoute('adminpr');
    }
    /**
     * @Route("/products/create", name="pr_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function createProduct(Request $request): Response
    {
        $product = new Product();
        $product->setCreatedAt(new DateTime(null, new DateTimeZone('Europe/Athens')));
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repo = $this->getDoctrine()->getRepository(Product::class);
            if ($repo->count(['code'=> $product->getCode()]) > 0){
                #code 400 bad request
                return $this->render('admin/new.html.twig', [
                    'errors' => ['A product with this code exista already!'],
                    'product' => $product,
                    'form' => $form->createView(),
                ]);

            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('adminpr');
        }

        return $this->render('admin/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

}