<?php

namespace App\Controller;

use App\Entity\{
    ContactRequest,
};
use Doctrine\ORM\EntityManagerInterface;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response};
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Display the home page
     */
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AccueilController'
        ]);
    }

    /**
     * Send a mail
     */
    #[Route('/mail', name: 'mail')]
    public function mail(): Response
    {
        try {
            //Server settings
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = 'b3ad9c94683935';
            $mail->Password = '9313865826b5f4';                            //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('from@example.com', 'Mailer');
            $mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
            $mail->addAddress('ellen@example.com');               //Name is optional
            $mail->addReplyTo('info@example.com', 'Information');
            $mail->addCC('cc@example.com');
            $mail->addBCC('bcc@example.com');

            //Attachments
            $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Here is the subject';
            $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo 'Message has been sent';
        } catch (\Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'AccueilController'
        ]);
    }

    /**
     * Get friend requests for the current user
     * @param Request $request
     * @return Response
     */
    #[Route('/myNotifications', name: 'myNotifications')]
    public function myNotifications(Request $request): Response
    {
        $contactRequest = $this->entityManager->getRepository(ContactRequest::class)
            ->findOneBy([
                'target' =>$this->getUser()->getId(),
                'flag_state' => ContactRequest::REQUEST_CONTACT_PENDING
            ]);
        ($contactRequest)?$response=true:$response=false;
        return new Response($response);
    }

}
