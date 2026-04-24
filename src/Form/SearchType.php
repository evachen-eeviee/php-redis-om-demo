<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\User;
use App\Model\BookEnum;
use App\Model\SearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'label' => 'Titre',
            ])
            ->add('author', ChoiceType::class, [
                'choices' => (array) $options['authors'],
                'choice_value' => fn (?User $user) => $user ? $user->id : '',
                'choice_label' => fn (User $user) => $user->name,
                'required' => false,
                'label' => 'Auteur',
            ])
            ->add('category', ChoiceType::class, [
                'choices' => (array) $options['categories'],
                'choice_value' => fn (?Category $cat) => $cat ? $cat->id : '',
                'choice_label' => fn (Category $cat) => $cat->category,
                'label' => 'Catégorie',
                'placeholder' => 'Choisir la catégorie',
                'required' => false,
            ])
            ->add('enum', EnumType::class, [
                'class' => BookEnum::class,
                'label' => 'Format du livre',
                'choice_label' => fn (BookEnum $choice) => match($choice){
                    BookEnum::BOOK =>'Livre Classique',
                    BookEnum::POCKET => 'Livre Pocket',
                    BookEnum::REVUE => 'Magazine',
                },
            ])
            ->add('unavailable', CheckboxType::class, [
                'label' => 'Indisponible',
                'required' => false,
                'attr' => [
                    'class' => 'rounded border-gray-300 text-indigo-600 focus:ring-indigo-500',
                ],
            ])
            ->add('priceMin', TextType::class, [
                'required' => false,
                'label' => 'Prix Minimum',
            ])
            ->add('priceMax', TextType::class, [
                'required' => false,
                'label' => 'Prix Maximum',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'categories' => [],
            'authors' => [],
        ]);
        $resolver->setAllowedTypes('categories', 'array');
    }
}
