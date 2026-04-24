<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\User;
use App\Model\BookEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('bookEnum', EnumType::class, [
                'class' => BookEnum::class,
                'label' => 'Format du livre',
                'choice_label' => fn (BookEnum $choice) => match($choice){
                    BookEnum::BOOK =>'Livre Classique',
                    BookEnum::POCKET => 'Livre Pocket',
                    BookEnum::REVUE => 'Magazine',
                },
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('author', ChoiceType::class, [
                'choices' => (array) $options['authors'],
                'choice_value' => fn (?User $user) => $user ? $user->id : '',
                'choice_label' => fn (User $user) => $user->name,
                'label' => 'Auteur',
            ])
            ->add('category', ChoiceType::class, [
                'choices' => (array) $options['categories'],
                'choice_value' => fn (?Category $cat) => $cat ? $cat->id : '',
                'choice_label' => fn (Category $cat) => $cat->category,
                'label' => 'Catégorie',
            ])
            ->add('price', NumberType::class)
            ->add('description', TextareaType::class)
            ->add('enabled', CheckboxType::class, [
                'label' => 'Rendre le livre visible ?',
                'required' => false,
                'attr' => [
                    'class' => 'rounded border-gray-300 text-indigo-600 focus:ring-indigo-500',
                ],
            ])
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

        $resolver->setAllowedTypes('authors', 'array');
        $resolver->setAllowedTypes('categories', 'array');
    }
}
