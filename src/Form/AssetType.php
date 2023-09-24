<?php

namespace App\Form;

use App\Entity\Asset;
use App\Enum\AssetCategoryTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('location')
            ->add('category', ChoiceType::class, [
                'choices' => AssetCategoryTypeEnum::getTypesArray(),
            ])
            ->add('code')
            ->add('name', null, [
                'attr' => [
                    'style' => 'text-transform: uppercase;',
                ],
            ])
            ->add('price', MoneyType::class, [
                'currency' => 'USD',
                'divisor' => 100,
            ])
            ->add('cost', MoneyType::class, [
                'currency' => 'USD',
                'divisor' => 100,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Asset::class,
        ]);
    }
}
