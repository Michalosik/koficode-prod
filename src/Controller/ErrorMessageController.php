<?php

namespace App\Controller;

use App\Entity\ContactEntry;
use App\Form\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class ErrorMessageController extends AbstractController
{
    #[Route('/error', name: 'error')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $tryMessage = new ContactEntry();
        $contactForm = $this->createForm(ContactFormType::class, $tryMessage);
        $contactForm->handleRequest($request);
        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $name = $contactForm->get('name')->getData();
            $message = $contactForm->getData();
            try {

                $confirmMail = (new TemplatedEmail())
                    ->from(new Address('info@koficode.pl', 'Koficode - idealna strona dla Ciebie!'))
                    ->to($contactForm->get('mail')->getData())
                    ->subject($name . '! Potwierdzam otrzymanie Twojej wiadomości')
                    ->replyTo('info@koficode.pl')
                    ->htmlTemplate('emails/confirm.html.twig')
                    ->context([
                        'userData' => $message
                    ]);
                $notificationMail = (new TemplatedEmail())
                    ->from(new Address('info@koficode.pl', 'Koficode - idealna strona dla Ciebie!'))
                    ->to('michal_balinski@o2.pl')
                    ->subject('Otrzymałeś wiadomość od ' . $name . '!')
                    ->replyTo($contactForm->get('mail')->getData())
                    ->htmlTemplate('emails/notification.html.twig')
                    ->context(['userData' => $message]);
                $mailer->send($confirmMail);
                $mailer->send($notificationMail);
                $this->redirectToRoute('success');
            } catch (\Exception $e) {
                $this->redirectToRoute('error');
            }
        }
        return $this->render('messages/error.html.twig', [
            'controller_name' => 'ErrorMessageController',
            'contactForm' => $contactForm->createView()
        ]);
    }
}
