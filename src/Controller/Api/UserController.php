<?php


namespace App\Controller\Api;


use App\Entity\User;
use App\Form\UserEditType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api/v1/user", defaults={"_format":"json"})
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user", methods={"GET"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $email = $request->query->get('email');
        $limit = $request->query->get('limit', 8);
        $page = $request->query->get('page', 1);

        if($limit <= 1){
            throw new BadRequestHttpException('Limit can not be negative or zero');
        }
        $pageNum = $userRepository->countPages( $email,$limit);
        $users = $userRepository->filter( $email, $limit, $page);
        if(!($users) && in_array($page, range(1, $pageNum))){
            throw new BadRequestHttpException("400");
        }
        if($page <= 0 || $page > $pageNum){
            throw new BadRequestHttpException('Invalid page number');
        }
        if ($limit > 100) {
            throw new BadRequestHttpException('Limit can not exceed 100 users');
        }
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        return new Response($serializer->serialize(['users' => $users,
            'currentValues' => [
                'limit' => $limit,
                'page' => $page,
                'email' => $email,
            ],

            'totalPages' => $pageNum
            ], 'json',
            ['ignored_attributes' => ['id', 'apiToken', 'password', 'cart', 'orders', 'salt']]), 200);
    }

    /**
     * @Route("/", name="user_create", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $parameters = json_decode($request->getContent(), true);
        $user = new User();
        $entityManager = $this->getDoctrine()->getManager();

        $user->setEmail($parameters['email']);
        $user->setPassword($encoder->encodePassword($user, $parameters['password']));
        $user->setRoles($parameters['roles']);
        $user->setIsVerified(false);

        $entityManager->persist($user);
        $entityManager->flush();
        return new Response(null, 201);
    }

    /**
     * @Route("/{id}", name="user_update", methods={"PATCH"})
     * @param Request $request
     * @param int $id
     * @param UserRepository $repo
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Request $request,
                         int $id,
                         UserRepository $repo,
                        EntityManagerInterface $entityManager): Response
    {
        $parameters = json_decode($request->getContent(), true);
        $user = $repo->findOneBy(['id' => $id]);
        $form = $this->createForm(UserEditType::class, $user);
        $user->setRoles($parameters['roles']);
        $form->submit($parameters);

        $entityManager->persist($user);
        $entityManager->flush();
        return new Response(null, 200);
    }

    /**
     * @Route("/{id}", name="user_del", methods={"DELETE"})
     * @param int $id
     * @param UserRepository $repo
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(int $id, UserRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $user = $repo->findOneBy(['id' => $id]);
        if(!$user) throw new NotFoundHttpException('User not found');
        $entityManager->remove($user);
        $entityManager->flush();
        return new Response();
    }
}