<?php

namespace App\Form\Type;

use App\Form\DataTransformer\CentimesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['divide'] === false) {
            return;
        }
        //si l'options divide n'est pas egale a false j'utilise mon data Transformer pour diviser, et l'utilisateur verra des euro et 
        //pourra remplir le champ en euro alors que ce que l'on recoit en bdd est des centimes.
        $builder->addModelTransformer(new CentimesTransformer);
    }
    public function getParent()
    {
        return NumberType::class;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'divide' => true
        ]);
    }
}
