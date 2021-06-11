<?php


namespace App\Controller;


use App\Entity\Order;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class MailerController extends AbstractController
{
    /**
     * @Route("/emailStatus")
     */
    public function sendEmail(MailerInterface $mailer, Order $order)
    {
//        ->to($user->getEmail())
//        ->subject('Please Confirm your Email')
//
        $email = (new TemplatedEmail())
            ->from('simple.store@gmail.com')
            ->to( new Address($order->getUser()->getEmail()))
            ->subject("Order details")
//            ->text('Order status changed to '. $order->getStatus());
            ->htmlTemplate('emails/status_change.html.twig')
            ->context([
                'order' => $order
            ]);
        $loader = new FilesystemLoader('templates');

        $twigEnv = new Environment($loader);

        $twigBodyRenderer = new BodyRenderer($twigEnv);

        $twigBodyRenderer->render($email);

        $mailer->send($email);
//            ->htmlTemplate('registration/confirmation_email.html.twig');
//            ->html('<p>See Twig integration for better HTML integration!</p>');

        //$mailer->send($email);
    }


}