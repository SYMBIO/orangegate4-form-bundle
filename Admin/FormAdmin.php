<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 30.10.15
 * Time: 9:44
 */

namespace Symbio\OrangeGate\FormBundle\Admin;

use Symbio\OrangeGate\AdminBundle\Admin\Admin as BaseAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;


class FormAdmin extends BaseAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('translations', 'orangegate_translations', [
                'translation_domain' => $this->translationDomain,
                'label' => false,
                'fields' => [
                    'name' => ['label' => 'label.form_name'],
                    'description' => ['label' => 'label.form_description'],
                ],
            ])
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', 'url', ['label' => 'label.form_id'])
            ->add('name', 'string', ['label' => 'label.form_name'])
            ->add('description', 'textarea', ['label' => 'label.form_description'])
            ->add('_action', 'actions', [
                'label' => 'label.form_actions',
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'fields' => ['template' => 'SymbioOrangeGateFormBundle:FormAdmin:fields_button.html.twig'],
            ]])
        ;
    }
}