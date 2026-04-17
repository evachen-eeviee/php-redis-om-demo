<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $comment = $options['data'] ?? null;
        $bookTitle = $comment && $comment->book ? $comment->book->title : 'Livre inconnu';
        $builder
            ->add('book_display', TextType::class, [
                'label' => 'Livre concerné : ',
                'mapped' => false,
                'data' => $bookTitle,
                'disabled' => true,
            ])
            ->add('author', ChoiceType::class, [
                'choices' => (array) $options['authors'],
                'choice_value' => fn (?User $user) => $user ? $user->id : '',
                'choice_label' => fn (User $user) => $user->name,
                'label' => 'Auteur',
            ])
            ->add('content', TextareaType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'authors' => [],
        ]);
    }
}
