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

class FieldType extends AbstractType
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
        return 'orangegate_form_field_type';
    }

    public static function getChoices()
    {
        return [
            'text'       => 'option.fieldtype_text',
            'textarea'   => 'option.fieldtype_textarea',
            'select'     => 'option.fieldtype_select',
            'radio'      => 'option.fieldtype_radio',
            'checkboxes' => 'option.fieldtype_checkboxes',
            'captcha'    => 'option.fieldtype_captcha',
        ];
    }
}