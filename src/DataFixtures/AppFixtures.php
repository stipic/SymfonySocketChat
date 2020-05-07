<?php
namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\User;
use App\Entity\Conversation;
use App\Entity\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private const USER_PASSWORD = '$2y$13$tiPZRrMWPHe2xp3qDhLt1e0J0Kayy8.m2G5LjV83uWeMXmjD0X8J2'; // 123321

    public function load(ObjectManager $manager)
    {
        $roles = [
            'ROLE_SUPERADMIN',
            'ROLE_MODERATOR',
            'ROLE_USER',
        ];

        $users = [
            // ADMINs
            [
                'username' => 'kristijan',
                'password' => self::USER_PASSWORD,
                'email' => 'kiki.stipic@gmail.com',
                'displayName' => 'ExtremePower',
                'role' => $roles[0]
            ],

            // MODERATORs

            [
                'username' => 'moderator',
                'password' => self::USER_PASSWORD,
                'email' => 'moderator@example.com',
                'displayName' => 'Moderate Guy',
                'role' => $roles[1]
            ],

            // USERs

            [
                'username' => 'user',
                'password' => self::USER_PASSWORD,
                'email' => 'user@example.com',
                'displayName' => 'User Guy',
                'role' => $roles[2]
            ]

        ];

        foreach(array_keys($users) as $key)
        {
            $user = new User();
            $user->setEmail($users[$key]['email']);
            $user->setUsername($users[$key]['username']);
            $user->setPassword($users[$key]['password']);
            $user->setDisplayName($users[$key]['displayName']);
            $user->setRole($users[$key]['role']);

            $referenceId = 'user-' . $key;
            $this->addReference($referenceId, $user);

            $manager->persist($user);
        }

        $manager->flush();

        // Create ManyToMany conversations for each user.

        $maxUsers = count($users);
        for($firstUserKey = 0; $firstUserKey < $maxUsers; $firstUserKey ++)
        {
            for($secondUserKey = $firstUserKey; $secondUserKey < $maxUsers; $secondUserKey ++)
            {
                if($firstUserKey !== $secondUserKey)
                {
                    $conversation = new Conversation();
                    $conversation->setIsChannel(false);
                    $conversation->setDeleted(false);

                    $firstUserReferenceId = 'user-' . $firstUserKey;
                    $firstUserObject = $this->getReference($firstUserReferenceId);

                    $secondUserReferenceId = 'user-' . $secondUserKey;
                    $secondUserObject = $this->getReference($secondUserReferenceId);

                    $conversation->setConversationNameForGuest($firstUserObject);
                    $conversation->setConversationNameForOwner($secondUserObject);

                    $conversation->setCreatedBy($firstUserObject);
                    $conversation->addUserToConversation($firstUserObject);
                    $conversation->addUserToConversation($secondUserObject);

                    $manager->persist($conversation);
                }
            }
        }

        $manager->flush();
    }
}