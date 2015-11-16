<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 13.11.15
 * Time: 9:48
 */

namespace Symbio\OrangeGate\FormBundle\Service;

use Symbio\OrangeGate\FormBundle\Entity\Field;
use Symbio\OrangeGate\FormBundle\Entity\Form as FormEntity;
use Symfony\Component\Form\FormFactory as Factory;
use Symfony\Component\Form\Form;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FormFactory
 * @package Symbio\OrangeGate\FormBundle\Service
 */
class FormFactory
{
    /**
     * @var Factory
     */
    protected $formFactory;

    /**
     * @var TranslatorInterface
     */
    protected $translator;


    /**
     * FormFactory constructor.
     * @param Factory $formFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(Factory $formFactory, TranslatorInterface $translator)
    {
        $this->formFactory = $formFactory;
        $this->translator = $translator;
    }


    /**
     * Creates form according to given form entity
     * @param FormEntity $formEntity
     * @return Form
     */
    public function createForm(FormEntity $formEntity)
    {
        // todo data
        $formBuilder = $this->formFactory->createBuilder('form', [], []);

        foreach ($formEntity->getFields() as $field) {
            $formBuilder->add('field_' . $field->getId(), $field->getFormFieldType(), $field->getFormFieldParams());
        }
        $formBuilder->add('submit', 'submit', ['label' => $this->translator->trans('form.button_submit', [], 'SymbioOrangeGateFormBundle')]);

        // todo set up validation?

        // todo form logic! somewhere
        return $formBuilder->getForm();
    }


    public function getFormData(FormEntity $formEntity, Form $form)
    {
        $rawdata = $form->getData();

        $ret = [];
        /** @var Field $field */
        foreach ($formEntity->getFields() as $field) {
            $valueIndex = 'field_' . $field->getId();

            if ($field->isChoice()) {
                $choices = $field->getChoicesArray();

                if (is_array($rawdata[$valueIndex])) {
                    $value = [];
                    foreach ($rawdata[$valueIndex] as $choice) {
                        $value[] = $choices[$choice];
                    }
                } else {
                    $value = $choices[$rawdata[$valueIndex]];
                }

            } else {
                $value = $rawdata[$valueIndex];
            }

            $ret[$valueIndex] = [
                'label' => $field->getLabel(),
                'value' => $value,
            ];
        }

        return $ret;
    }
}