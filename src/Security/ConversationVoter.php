<?php
namespace App\Security;

use App\Entity\Conversation;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ConversationVoter extends Voter
{
    private const ACTION_ACCESS = 'access';

    protected function supports($attribute, $subject)
    {
        if(!in_array($attribute, [
            self::ACTION_ACCESS
        ])) 
        {
            return false;
        }

        if(!$subject instanceof Conversation) 
        {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        $conversation = $subject;

        switch($attribute) 
        {
            case self::ACTION_ACCESS:
                return $this->canAccess($conversation, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canAccess(Conversation $conversation, User $user)
    {
        //@todo ovo bi moglo biti sporo i necemo uvijek dohvacati sve usere za svaki conversation
        //treba napisati novu repository metodu s kojom cemo ovo provjeriti.
        foreach($conversation->getUsers()->getValues() as $allowedUser)
        {
            if($allowedUser->getId() === $user->getId())
            {
                return true;
            }
        }
        return false;
    }
}