<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('author', ChoiceType::class, [
                'choices' => (array) $options['authors'],
                'choice_label' => fn(User $user) => $user->name,
                'label' => 'Auteur',
            ])
            ->add('category', ChoiceType::class, [
                'choices' => (array) $options['categories'],
                'choice_label' => fn(Category $category) => $category->category,
                'label' => 'Catégorie'
            ])
            ->add('price', NumberType::class)
            ->add('description', TextareaType::class)
            ->add('enabled', CheckboxType::class)
            ->add('publishedAt', DateType::class, [
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
            'authors' => [],
            'categories' => [],
        ]);
    }
}
