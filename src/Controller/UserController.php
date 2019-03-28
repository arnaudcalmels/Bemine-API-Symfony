<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Person;
use App\Entity\Wedding;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/signup", name="signup", methods={"POST"})
     */
    public function signup(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);
   
        //je récupère mes données du front
        $email = $contentDecode->email;
        
        //si l'email existe déjà en base, je renvoie un message
        $alreayUser = $userRepository->findByEmail($email);
        if ($alreayUser){

            $data = 
            [
                'message' => 'l\'email du user existe déjà.'
            ]
            ;

            $response = new JsonResponse($data, 400);
        
            return $response;
            
        }
        
        //je récupère le reste de mes données du front
        // $urlAvatar = $contentDecode->urlAvatar;
        $firstname = $contentDecode->firstnameUser;
        $lastname = $contentDecode->nameUser;
        $spouseFirstname = $contentDecode->firstnamePartner;
        $spouseLastname = $contentDecode->namePartner;
        $weddingDate = $contentDecode->weddingDate;
        
        //Je crée une nouvelle instance de wedding car chaque nouveau user implique la création de son wedding.
        $wedding = new Wedding();
        $wedding->setDate(\DateTime::createFromFormat('Y-m-d', $weddingDate));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($wedding);

        //j'initialise les events type de mon mariage
        
        $event1 = new Event();
        $event1->setName('Cérémonie');
        $event1->setWedding($wedding);
        $event1->setActive(false);

        $event2 = new Event();
        $event2->setName('Vin d\'honneur');
        $event2->setWedding($wedding);
        $event2->setActive(false);

        $event3 = new Event();
        $event3->setName('Réception');
        $event3->setWedding($wedding);
        $event3->setActive(false);

        $event4 = new Event();
        $event4->setName('Brunch');
        $event4->setWedding($wedding);
        $event4->setActive(false);

        $entityManager->persist($event1);
        $entityManager->persist($event2);
        $entityManager->persist($event3);
        $entityManager->persist($event4);

        //je crée mon nouveau user 
        $user = new User();
        //petite interrogation sur la récupération du password
        // $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
        $encodedPassword = $passwordEncoder->encodePassword($user, $contentDecode->password);
        $user->setPassword($encodedPassword);
        $user->setEmail($email);
        // $user->setUrlAvatar($urlAvatar);
        $user->setWedding($wedding);
        // $user->setRoles(['ROLE_USER']);
        
        //je crée ma nouvelle personne, car un user est aussi une personne.
        $person = new Person();
        $person->setNewlyweds(true);
        $person->setFirstname($firstname);
        $person->setLastname($lastname);
        $person->setMenu('ADULTE');
        $person->setWedding($wedding);
        $person->setAttendance(1);
        
        //je crée le deuxième marié
        $personSpouse = new Person();
        $personSpouse->setFirstname($spouseFirstname);
        $personSpouse->setLastname($spouseLastname);
        $personSpouse->setNewlyweds(true);
        $personSpouse->setMenu('ADULTE');
        $personSpouse->setWedding($wedding);
        $personSpouse->setAttendance(1);
        
        $entityManager->persist($user);
        $entityManager->persist($wedding);
        $entityManager->persist($person);
        $entityManager->persist($personSpouse);
        $entityManager->flush();
        
        //je set mon flash message avec symfo, voir si c'est fait avec react ou pas
        $this->addFlash(
            'success',
            'Votre compte a bien été crée, merci de vous connecter.'
        );

        
        $data = 
            [
                'message' => 'Votre compte a bien été crée.'
            ]
        ;

        $response = new JsonResponse($data, 200);
       
        return $response;

    }

    /**
     * @Route("/brides/profil/{userId}", name="profil", methods={"GET"})
     */
    public function profil(UserRepository $userRepository, $userId)
    {
        // je récupère mon user connecté grâce à l'id du user passée en url
        $thisUser = $userRepository->findUserProfilQueryBuilder($userId);

        if (!$thisUser){
            
            $data = 
            [
                'message' => 'Le user id n\'existe pas.'
            ]
            ;
            
            $response = new JsonResponse($data, 400);
        
            return $response;
        }

        $data = 
            [
                'thisUser' => $thisUser
            ]
        ;

        $response = new JsonResponse($data, 200);
       
        return $response;
    }
}
