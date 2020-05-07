<?php
namespace App\Service;

use App\Entity\User;
use Twig\Environment;
use App\Entity\Conversation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Routing\RouterInterface;

class ConversationHandler
{
    private $_router;

    private $_em;

    private $_validator;

    private $_zmqPusher;

    private $_twig;

    public function __construct(
        $amqpPusher, 
        RouterInterface $router, 
        EntityManagerInterface $em, 
        Environment $twig
    )
    {
        $this->_router = $router;
        $this->_em = $em;
        $this->_validator = Validation::createValidator();
        $this->_zmqPusher = $amqpPusher;
        $this->_twig = $twig;
    }

    public function getUserConversations(User $user, Conversation $currentConversation)
    {
        //@todo trebamo napraviti query koji ce nam vratiti:
        // popis svih konverzacija od nekog usera i uz to vratiti 
        // popis SVIH userId-eva koji su u tom razgovoru.

        $userConversations = $user->getConversations()->getValues();
        $sortedConversations = ['channels' => [], 'direct' => [], 'current' => []];
        foreach($userConversations as $singleUserConversation)
        {
            $numberOfUnreadedMessages = $this->getUserConversationUnreadedMessages($user, $singleUserConversation);
            $conversationRoute = $this->_router->generate('app_conversation', ['id' => $singleUserConversation->getId()]);
            $isActive = false;
            if($singleUserConversation === $currentConversation)
            {
                $isActive = true;
                if($numberOfUnreadedMessages > 0)
                {
                    // oznaci sve poruke kao procitane
                    foreach($user->getUnreadedMessages()->getValues() as $unreadedMessage)
                    {
                        if($unreadedMessage->getMessageBlock()->getConversation() == $currentConversation)
                        {
                            $user->removeUnreadedMessage($unreadedMessage);
                            $this->_em->persist($user);
                        }
                    }
                    $this->_em->flush();
                }
            }
            
            if($singleUserConversation->getIsChannel() == true)
            {
                $sortedConversations['channels'][] = [
                    'id' => $singleUserConversation->getId(),
                    'title' => $singleUserConversation->getChannelName(),
                    'subtitle' => 'Channel.',
                    'route' => $conversationRoute,
                    'active' => $isActive,
                    'isChannel' => $singleUserConversation->getIsChannel(),
                    'userIdInConversation' => $user->getId(),
                    'isChannelPublic'=> $singleUserConversation->getIsChannelPublic(),
                    'unreadedMessages' => $numberOfUnreadedMessages
                ];

                if($isActive === true)
                {
                    $sortedConversations['current'] = end($sortedConversations['channels']);
                }
            }
            else 
            {
                $conversationName = $singleUserConversation->getConversationNameForOwner()->getDisplayName();
                $conversationSubtitle = $singleUserConversation->getConversationNameForOwner()->getUsername();
                if($singleUserConversation->getCreatedBy() != $user)
                {
                    $conversationName = $singleUserConversation->getConversationNameForGuest()->getDisplayName();
                    $conversationSubtitle = $singleUserConversation->getConversationNameForGuest()->getUsername();
                }

                //@todo ovo napraviti pametnije, ali moramo proci kroz ovaj conversation i pronaci sami sebe u tom razgovoru
                // i vratiti ID nas, za nas ce to onda biti usid u direktnim razgovorima.
                $userIdInConversation = '';
                foreach($singleUserConversation->getUsers()->getValues() as $userInConversation)
                {
                    if($userInConversation != $user)
                    {
                        $userIdInConversation = $userInConversation->getId();
                    }
                }
                
                $sortedConversations['direct'][] = [
                    'id' => $singleUserConversation->getId(),
                    'title' => $conversationName,
                    'subtitle' => $conversationSubtitle,
                    'route' => $conversationRoute,
                    'active' => $isActive,
                    'isChannel' => $singleUserConversation->getIsChannel(),
                    'userIdInConversation' => $userIdInConversation,
                    'isChannelPublic'=> $singleUserConversation->getIsChannelPublic(),
                    'unreadedMessages' => $numberOfUnreadedMessages
                ];

                if($isActive === true)
                {
                    $sortedConversations['current'] = end($sortedConversations['direct']);
                }
            }
        }

        return $sortedConversations;
    }

    // metoda vraca broj neprocitanih poruka za jednu konverzaciju od nekog usera
    public function getUserConversationUnreadedMessages(User $user, Conversation $currentConversation)
    {
        $response = $this->_em->getRepository(\App\Entity\User::class)->findNumberOfUnreadedMessages($user->getId(), $currentConversation->getId());
        if(isset($response[0]))
        {
            return (int) $response[0]['count'];
        }

        return 0;
    }

    public function createNewConversation(
        string $channelName,
        User $author,
        bool $isChannel,
        bool $isChannelPublic,
        bool $isDeleted,
        $usersToAddInConversation,
        Conversation $currentConversation
    ) : array
    {
        $responseErrors = [];
        $responseSuccess = false;

        $channelName = trim($channelName);
        $channelName = htmlspecialchars($channelName, ENT_QUOTES);

        $conversation = new Conversation();
        $conversation->setChannelName($channelName);
        $conversation->setCreatedBy($author);
        $conversation->setIsChannel($isChannel);
        $conversation->setIsChannelPublic($isChannelPublic);
        $conversation->setDeleted($isDeleted);

        $errors = $this->_validator->validate($conversation);

        if(count($errors) == 0)
        {
            foreach($usersToAddInConversation as $user)
            {
                $user->addConversation($conversation);
                $this->_em->persist($user);
            }

            $this->_em->persist($conversation);
            $this->_em->flush();

            // nakon sto je flushano, mogu pushati svima njima novi sidebar
            foreach($usersToAddInConversation as $user)
            {
                $sortedConversations = $this->getUserConversations($user, $currentConversation);
                $receiverPayload = $this->_twig->render('inc/discussion-section.inc.html.twig', array(
                    'conversations' => $sortedConversations,
                ));
                $this->_zmqPusher->push($receiverPayload, 'app_unread_messages', ['username' => $user->getUsername()]);
            }

            $responseSuccess = true;
        }
        else 
        {
            foreach($errors as $violation) 
            {
                $responseErrors[$violation->getPropertyPath()] = $violation->getMessage();
            }
        }

        return [$responseSuccess, $responseErrors];
    }
}
