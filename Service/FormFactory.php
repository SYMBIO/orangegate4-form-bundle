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
use Symfony\Component\Validator\Constraints;
use Symbio\OrangeGate\FormBundle\Exception\InvalidConfigurationException;

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
     * @throws InvalidConfigurationException
     */
    public function createForm(FormEntity $formEntity)
    {
        // todo data
        $formBuilder = $this->formFactory->createBuilder('form', [], []);

        /** @var $field Field */
        // build form fields
        foreach ($formEntity->getFields() as $field) {
            $params = $field->getFormFieldParams();

            // required validator
            if ($field->isRequired()) {
                $params['constraints'] = [new Constraints\NotBlank()];
            }

            // other validators
            if ($field->getValidationType() !== null && $field->getValidationType() != 'none') {
                $validatorParams = [];

                if ($field->getValidationMessage() !== null) {
                    $validatorParams['message'] = $field->getValidationMessage();
                }

                switch ($field->getValidationType()) {
                    case 'email':
                        $validator = new Constraints\Email($validatorParams);;
                        break;
                    case 'number':
                        $validatorParams['type'] = 'numeric';
                        $validator = new Constraints\Type($validatorParams);
                        break;
                    case 'regexp':
                        $validatorParams['pattern'] = $field->getValidationSettings();
                        $validator = new Constraints\Regex($validatorParams);
                        break;
                    default:
                        throw new InvalidConfigurationException('Unknown validation type: ' . $field->getValidationType());
                }

                if (!array_key_exists('constraints', $params)) {
                    $params['constraints'] = [];
                }
                $params['constraints'][] = $validator;
            }

            // add field
            $formBuilder->add('field_' . $field->getId(), $field->getFormFieldType(), $params);
        }

        // submit button
        $formBuilder->add('submit', 'submit', ['label' => $formEntity->getSubmitLabel()]);

        // honeypot
        $formBuilder->add('email', 'email', [
            'label' => $this->translator->trans('form.label_honeypot', [], 'SymbioOrangeGateFormBundle'),
            'required' => false,
            'constraints' => [new Constraints\Blank()]
        ]);

        // return form
        return $formBuilder->getForm();
    }


    /**
     * Builds associative array with items like field_id => [label, submitted_data]
     * @param FormEntity $formEntity
     * @param Form $form
     * @return array
     */
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