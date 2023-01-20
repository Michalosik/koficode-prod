<?php

namespace App\Controller;

use App\Entity\ContactEntry;
use App\Form\ContactFormType;
use App\Repository\ContactEntryRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Doctrine\Persistence\ManagerRegistry;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, ManagerRegistry $doctrine, MailerInterface $mailer): Response
    {
        $message = new ContactEntry();
        $contactForm = $this->createForm(ContactFormType::class, $message);
        $contactForm->handleRequest($request);
        $em = $doctrine->getManager();
       	$recaptchaToken = $request->get('g-recaptcha-response');
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => 'xxxxxxxxxxxxxxxxxxx',
            'response' => $recaptchaToken,
        ];
        $options = [
          'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $responseDecoded = json_decode($response, true);
        if ($contactForm->isSubmitted() && $contactForm->isValid() && $responseDecoded['success'] && $responseDecoded['score'] > 0.5 ) {
            $name = $contactForm->get('name')->getData();
            $mail = $contactForm->get('mail')->getData();
            $content = $contactForm->get('content')->getData();
            $isAgree = $contactForm->get('isAgree')->getData();
          $hp = $contactForm->get('hp')->getData();
            if (trim($name) === '' || trim($mail) === '' || trim($content) === '' || $isAgree == false) {
                $this->addFlash('empty', 'Nie wszystkie pola zostały wypełnione poprawnie, prosimy o sprawdzenie danych i przesłanie formularza raz jeszcze.');
            } else {
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
                  
                    $em->persist($message);
                    $em->flush();
                    $mailer->send($confirmMail);
                    $mailer->send($notificationMail);
                    $this->addFlash('success', 'Formularz został poprawnie wysłany. Dziękujemy za kontakt odezwiemy się jak najszybciej!');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Niestety coś poszło nie tak, prosimy o sprawdzenie wszystkich danych i przesłanie formularza jeszcze raz.');
                }
            }
        }

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'contactForm' => $contactForm->createView()
        ]);
    }
}
