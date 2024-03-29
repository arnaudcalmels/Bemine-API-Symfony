<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker\Factory;
use App\Entity\Wedding;
use App\Entity\GuestGroup;
use App\Entity\Person;
use App\Entity\Event;
use App\Entity\Mail;
use App\Utils\RandomString;

class AppFixtures extends Fixture
{

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder =$passwordEncoder;
    }

    
    public function load(ObjectManager $manager)
    {
        $rm = new RandomString();

        $couple1 = new User();
        $couple1->setEmail('couple1@test.fr');
        $encodedPassword = $this->passwordEncoder->encodePassword($couple1, 'couple1');
        $couple1->setPassword($encodedPassword);
        $couple1->setRoles('{"name": "Couple", "code": "ROLE_COUPLE"}');

        $couple2 = new User();
        $couple2->setEmail('couple2@test.fr');
        $encodedPassword = $this->passwordEncoder->encodePassword($couple2, 'couple2');
        $couple2->setPassword($encodedPassword);
        $couple2->setRoles('{"name": "Couple", "code": "ROLE_COUPLE"}');

        $admin = new User();
        $admin->setEmail('admin@test.fr');
        $encodedPassword = $this->passwordEncoder->encodePassword($admin, 'admin');
        $admin->setPassword($encodedPassword);
        $admin->setRoles('{"name": "Administrateur", "code": "ROLE_ADMIN"}');

        $manager->persist($admin);
        $manager->persist($couple1);
        $manager->persist($couple2);

        $generator = Factory::create('fr_FR');
        $populator = new \Faker\ORM\Doctrine\Populator($generator, $manager);

        $wedding1 = new Wedding();
        $wedding1->setUser($couple1);
        $wedding1->setDate($generator->dateTimeBetween('+6 months', '+1 years'));

        $wedding2 = new Wedding();
        $wedding2->setUser($couple2);
        $wedding2->setDate($generator->dateTimeBetween('+6 months', '+1 years'));

        $manager->persist($wedding1);
        $manager->persist($wedding2);


        // people de couple1

        $person1 = new Person();
        $person1->setLastname($generator->lastName());
        $person1->setFirstname($generator->firstName());
        $person1->setWedding($wedding1);
        $person1->setNewlyweds(true);
        $person1->setAttendance(1);

        $person2 = new Person();
        $person2->setLastname($generator->lastName());
        $person2->setFirstname($generator->firstName());
        $person2->setWedding($wedding1);
        $person2->setNewlyweds(true);
        $person2->setAttendance(1);

        $person3 = new Person();
        $person3->setLastname($generator->lastName());
        $person3->setFirstname($generator->firstName());
        $person3->setWedding($wedding2);
        $person3->setNewlyweds(true);
        $person3->setAttendance(1);

        $person4 = new Person();
        $person4->setLastname($generator->lastName());
        $person4->setFirstname($generator->firstName());
        $person4->setWedding($wedding2);
        $person4->setNewlyweds(true);
        $person4->setAttendance(1);

        $manager->persist($person1);
        $manager->persist($person2);
        $manager->persist($person3);
        $manager->persist($person4);


        $event1 = new Event();
        $event1->setName('Cérémonie');

        $event2 = new Event();
        $event2->setName('Vin d\'honneur');

        $event3 = new Event();
        $event3->setName('Réception');

        $event4 = new Event();
        $event4->setName('Brunch');

        $manager->persist($event1);
        $manager->persist($event2);
        $manager->persist($event3);
        $manager->persist($event4);

        
        $manager->flush();


        for ($i = 0; $i < 40; $i++) {

            if ($i < 15) { // guestGroups & people for 1st wedding
                $guestGroup = new GuestGroup();
                $guestGroup->setEmail($generator->email());
                $guestGroup->setSlugUrl($rm->random());
                $guestGroup->setWedding($wedding1);

                for ($n = 0; $n < 2; $n++) {
                    $person = new Person();
                    $person->setLastname($generator->lastName());
                    $person->setFirstname($generator->firstName());
                    $person->setWedding($wedding1);
                    $person->setNewlyweds(false);
                    $person->setAttendance($this->randomAttendance());
                    $guestGroup->addPerson($person);
                    $guestGroup->setWedding($wedding1);
                    if ($n == 0) {
                        $guestGroup->setContactPerson($person);
                    }
                    $manager->persist($person);
                    
                    $manager->persist($guestGroup);
                }
                
            } else { // guestGroups & people for 2nd wedding
                $guestGroup = new GuestGroup();
                $guestGroup->setEmail($generator->email());
                $guestGroup->setSlugUrl($rm->random());
                $guestGroup->setWedding($wedding2);

                for ($n = 0; $n < 2; $n++) {
                    $person = new Person();
                    $person->setLastname($generator->lastName());
                    $person->setFirstname($generator->firstName());
                    $person->setWedding($wedding2);
                    $person->setNewlyweds(false);
                    $person->setAttendance($this->randomAttendance());
                    $guestGroup->addPerson($person);
                    $guestGroup->setWedding($wedding2);
                    if ($n == 0) {
                        $guestGroup->setContactPerson($person);
                    }
                    $manager->persist($person);
                    
                    // $manager->flush();
                    $manager->persist($guestGroup);
                }
            }
            

            
        }
        // gifts for the 1st wedding
        $populator->addEntity('App\Entity\Gift', 30, array(
            'name' => function() use ($generator) { return $generator->word(); },
            'url' => function() use ($generator) { return $generator->url(); },
            'availability' => true,
            'wedding' => $wedding1
        ));

        // gifts for the 2nd wedding
        $populator->addEntity('App\Entity\Gift', 50, array(
            'name' => function() use ($generator) { return $generator->word(); },
            'url' => function() use ($generator) { return $generator->url(); },
            'availability' => true,
            'wedding' => $wedding2
        ));


        $populator->execute();



        $manager->flush();
    }

    public function randomAttendance()
    {
        $values= [0, 1, 2];

        return array_rand($values, 1);
    }
}
