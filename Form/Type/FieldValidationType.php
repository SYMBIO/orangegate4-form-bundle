<?php

/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 02.11.15
 * Time: 16:17
 */

namespace Symbio\OrangeGate\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FieldValidationType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'choices' => self::getChoices(),
            'translation_domain' => 'SymbioOrangeGateFormBundle',
        ]);
    }


    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'orangegate_form_field_validation_type';
    }

    /**
     * Returns key => label pairs of available choices
     * @return array
     */
    public static function getChoices()
    {
        return [
            'none' => 'option.fieldvalidationtype_none',
            'number' => 'option.fieldvalidationtype_number',
            'email' => 'option.fieldvalidationtype_email',
            'regexp' => 'option.fieldvalidationtype_regexp',
        ];
    }
}