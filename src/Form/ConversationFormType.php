<?php
namespace App\Form;

use App\Entity\Conversation;
use App\Entity\User;
use App\Repository\UserRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ConversationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $userId = $user->getId();

        $builder
            ->add('name')
            ->add('users', EntityType::class, array(
                'required' => true,
                'class' => User::class,
                'query_builder' => function(UserRepository $er) use($userId)
                {
                    // preskcem sebe posto se rucno dodaje onaj tko kreira
                    return $er->createQueryBuilder('u')
                        ->where('u.id != ' . $userId)
                        ->orderBy('u.username', 'ASC');
                },
                'choice_label' => 'username',
                'multiple' => true,
                // 'expanded' => true
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Conversation::class,
            'user' => User::class
        ]);
    }
}