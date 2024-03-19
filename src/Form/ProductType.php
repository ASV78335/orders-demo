<?php

namespace App\Form;

use App\Category\Application\Query\CategoryItem;
use App\Product\Application\Query\ProductDetails;
use App\Unit\Application\Query\UnitItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('code')
            ->add('category', ChoiceType::class, [
                'choices' => $options['categories'],
                'choice_label' => 'name',
                'choice_value' => function (CategoryItem $categoryItem = null) {
                    return $categoryItem ? $categoryItem->getUuid() : '';
                },
                'required' => true,
                'empty_data' => ''
            ])
            ->add('baseUnit', ChoiceType::class, [
                'choices' => $options['units'],
                'choice_label' => 'name',
                'choice_value' => function (UnitItem $unitItem = null) {
                    return $unitItem ? $unitItem->getUuid() : '';
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'data_class' => ProductDetails::class,
            'categories' => [],
            'units' => []
        ]);
    }
}
