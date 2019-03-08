<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Conversation;
use App\Form\ConversationFormType;

class ConversationController extends Controller
{
    /**
     * @Route("/conversation/new", name="app_new_conversation")
     * @Method({"GET"})
     */
    public function newConversation(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATOR');

        $conversation = new Conversation();
        $userRepository = $this->get('doctrine')->getManager()->getRepository('App:User');
        $users = $userRepository->findAll();
        
        $form = $this->createForm(ConversationFormType::class, $conversation, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $entityManager = $this->getDoctrine()->getManager();

            $name = $form->get('channelName')->getData();
            $selectedUsers = $form->get('users')->getData()->getValues();

            $conversation->setChannelName($name);
            $conversation->setCreatedBy($this->getUser());
            $conversation->setIsChannel(true);
            $conversation->setIsChannelPublic(false);
            $conversation->setDeleted(false);

            $this->getUser()->addConversation($conversation);
            foreach($selectedUsers as $user)
            {
                $user->addConversation($conversation);
                $entityManager->persist($user);
            }

            $entityManager->persist($conversation);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('page/new_conversation.html.twig', [
            'conversationForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/conversation/{id}", name="app_conversation")
     * @Method({"GET"})
     */
    public function conversation(Conversation $conversation, Request $request)
    {
        $this->denyAccessUnlessGranted('access', $conversation);

        //@todo ovo za svaku poruku radi novi SQL upit, trebalo bi kreirati repositoryMetodu koja jednim pozivom dohvaca.
        $conversationMessages = $conversation->getMessages()->getValues();
        
        //@todo trebamo napraviti query koji ce nam vratiti:
        // popis svih konverzacija od nekog usera i uz to vratiti 
        // popis SVIH userId-eva koji su u tom razgovoru.
        $userConversations = $this->getUser()->getConversations()->getValues();

        $sortedConversations = ['channels' => [], 'direct' => [], 'current' => []];
        foreach($userConversations as $singleUserConversation)
        {
            $conversationRoute = $this->generateUrl('app_conversation', ['id' => $singleUserConversation->getId()]);
            $isActive = false;
            if($singleUserConversation === $conversation)
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
                    'userIdInConversation' => $this->getUser()->getId(),
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
                if($singleUserConversation->getCreatedBy() != $this->getUser())
                {
                    $conversationName = $singleUserConversation->getConversationNameForGuest();
                }

                //@todo ovo napraviti pametnije, ali moramo proci kroz ovaj conversation i pronaci sami sebe u tom razgovoru
                // i vratiti ID nas, za nas ce to onda biti usid u direktnim razgovorima.
                $userIdInConversation = '';
                foreach($singleUserConversation->getUsers()->getValues() as $userInConversation)
                {
                    if($userInConversation != $this->getUser())
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

        return $this->render('page/conversation.html.twig', [
            'messages' => $conversationMessages,
            'conversations' => $sortedConversations
        ]);
    }
}