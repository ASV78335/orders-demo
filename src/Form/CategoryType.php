<?php

namespace App\Form;

use App\Category\Application\Query\CategoryDetails;
use App\Category\Application\Query\CategoryItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('code')
            ->add('parent', ChoiceType::class, [
                'choices' => $options['categories'],
                'choice_label' => 'name',
                'choice_value' => function (CategoryItem $categoryItem = null) {
                    return $categoryItem?->getUuid();
                },
                'required' => false,
                'empty_data' => ''
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'data_class' => CategoryDetails::class,
            'categories' => []
        ]);
    }
}
