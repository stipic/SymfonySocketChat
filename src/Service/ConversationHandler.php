<?php
namespace App\Service;

use App\Entity\Conversation;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class ConversationHandler
{
    private $_router;

    public function __construct(Router $router)
    {
        $this->_router = $router;
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
            $conversationRoute = $this->_router->generate('app_conversation', ['id' => $singleUserConversation->getId()]);
            $isActive = false;
            if($singleUserConversation === $currentConversation)
            {
                $isActive = true;
            }

            if($singleUserConversation->getIsChannel() == true)
            {
                $sortedConversations['channels'][] = [
                    'id' => $singleUserConversation->getId(),
                    'title' => $singleUserConversation->getChannelName(),
                    'route' => $conversationRoute,
                    'active' => $isActive,
                    'isChannel' => $singleUserConversation->getIsChannel(),
                    'userIdInConversation' => $user->getId(),
                    'isChannelPublic'=> $singleUserConversation->getIsChannelPublic()
                ];

                if($isActive === true)
                {
                    $sortedConversations['current'] = end($sortedConversations['channels']);
                }
            }
            else 
            {
                $conversationName = $singleUserConversation->getConversationNameForOwner();
                if($singleUserConversation->getCreatedBy() != $user)
                {
                    $conversationName = $singleUserConversation->getConversationNameForGuest();
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
                    'route' => $conversationRoute,
                    'active' => $isActive,
                    'isChannel' => $singleUserConversation->getIsChannel(),
                    'userIdInConversation' => $userIdInConversation,
                    'isChannelPublic'=> $singleUserConversation->getIsChannelPublic()
                ];

                if($isActive === true)
                {
                    $sortedConversations['current'] = end($sortedConversations['direct']);
                }
            }
        }

        return $sortedConversations;
    }
}