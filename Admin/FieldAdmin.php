<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 02.11.15
 * Time: 15:55
 */

namespace Symbio\OrangeGate\FormBundle\Admin;

use Symbio\OrangeGate\AdminBundle\Admin\Admin as BaseAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symbio\OrangeGate\FormBundle\Form\Type\FieldType;

class FieldAdmin extends BaseAdmin
{
    protected $parentAssociationMapping = 'form';

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('translations', 'orangegate_translations', [
                'translation_domain' => $this->translationDomain,
                'label' => false,
                'fields' => [
                    'label' => ['label' => 'label.field_label'],
                ],
            ])
            ->add('type', 'orangegate_form_field_type', ['label' => 'label.field_type'])
            ->add('required', null, ['label' => 'label.field_required'])
            ->add('priority', null, ['label' => 'label.field_priority'])
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('label')
            ->add('type')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', 'url', ['label' => 'label.field_id'])
            ->add('label', 'string', ['label' => 'label.field_label'])
            ->add('type', 'choice', [
                'label' => 'label.field_type',
                'choices' => FieldType::getChoices(),
                'catalogue' => 'SymbioOrangeGateFormBundle',
            ])
            ->add('required', 'boolean', ['label' => 'label.field_required'])
            ->add('priority', 'int', ['label' => 'label.field_priority'])
            ->add('_action', 'actions', [
                'label' => 'label.form_actions',
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'fields' => ['template' => 'SymbioOrangeGateFormBundle:FormAdmin:choices_button.html.twig'],
                ]])
        ;
    }
}