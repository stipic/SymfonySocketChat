<?php
namespace App\DataFixtures;

use App\Entity\Conversation;
use App\Entity\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private const NUMBER_OF_USERS = 10;

    private const NUMBER_OF_CONVERSATIONS = 20;

    private const NUMBER_OF_MESSAGES = 50;

    private const USER_PASSWORD = '$2y$13$tiPZRrMWPHe2xp3qDhLt1e0J0Kayy8.m2G5LjV83uWeMXmjD0X8J2';

    public function load(ObjectManager $manager)
    {
        $generator = \Faker\Factory::create();
        $populator = new \Faker\ORM\Doctrine\Populator($generator, $manager);
        $populator->addEntity('App:User', self::NUMBER_OF_USERS, array(
            'displayName' => $generator->username,
            'password' => self::USER_PASSWORD
        ));
        $populator->addEntity('App:Conversation', self::NUMBER_OF_CONVERSATIONS);
        $populator->addEntity('App:Message', self::NUMBER_OF_MESSAGES);
        $populator->execute();
    }
}