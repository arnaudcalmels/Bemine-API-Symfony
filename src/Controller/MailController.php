<?php

namespace App\Controller;


use App\Repository\MailRepository;
use App\Repository\GuestGroupRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PersonRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
* @Route("/brides/mail/", name="mail_")
*/
class MailController extends AbstractController
{

    /**
     * @Route("send", name="send", methods={"POST"})
     */
    public function sendEmail(GuestGroupRepository $ggRepo, Request $request, \Swift_Mailer $mailer, PersonRepository $pRepo, UserRepository $userRepo, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent());

        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepo->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        $guestGroupsId = $data->listMailing;
        $messages = [];

        
        foreach ($guestGroupsId as $id) {
            $guestGroup = $ggRepo->findOneBy(['id' => $id]);
            if (!$guestGroup) {
                $messages[] = 'Un email n\'a pas été expédié car il n\'y a pas de groupe avec l\'id ' . $id;
            } elseif ($userWedding != $guestGroup->getWedding()) {
                $messages[] = 'Un email n\'a pas été expédié car le groupe ' . $id . ' ne fait pas parti de ce mariage';
            } else {
            
            // $recipient = $guestGroup->getEmail(); // A commenter pour fonctionnement faker
            $recipient = 'oweddingteam@gmail.com'; // A décommenter pour fonctionnement faker
            $wedding = $guestGroup->getWedding();
            $newlyweds = $pRepo->findBy([
                'wedding' => $wedding,
                'newlyweds' =>true
            ]);

            $invitationEmail = (new \Swift_Message('Invitation Email'))
            ->setFrom('oweddingteam@gmail.com')
            ->setTo($recipient)
            ->setSubject('Invitation au mariage de ' . $newlyweds[0]->getFirstname() . ' et ' . $newlyweds[1]->getFirstname())
            ->setBody(
                $this->renderView(
                    'mail/invitation.html.twig', [
                        'newlywed1' => $newlyweds[0],
                        'newlywed2' => $newlyweds[1],
                        'wedding' => $wedding,
                        'guestGroup' => $guestGroup,
                        ]),
                'text/html'
            );

            $mailer->send($invitationEmail);

            $guestGroup->setMailStatus(true);
            $em->flush();

            }
        }


        if (!empty($messages)) {
            $httpCode = 400;
        } else {
            $httpCode = 200;
            $messages[] = 'Emails envoyés';
        }

        $response = new JsonResponse($messages, $httpCode);
       
        return $response;
    }


    /**
     * @Route("show", name="show", methods={"GET"})
     */
    public function showEmail(UserRepository $userRepo, GuestGroupRepository $ggRepo, Request $request, \Swift_Mailer $mailer, PersonRepository $pRepo)
    {

        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepo->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();
        
        $newlyweds = $pRepo->findBy([
            'wedding' => $userWedding,
            'newlyweds' =>true
        ]);
        

        return $this->render(
            'mail/preview.html.twig', [
                'newlywed1' => $newlyweds[0],
                'newlywed2' => $newlyweds[1],
                'wedding' => $userWedding,
                ]
            );
    }
}