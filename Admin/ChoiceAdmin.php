<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 12.11.15
 * Time: 15:01
 */

namespace Symbio\OrangeGate\FormBundle\Admin;

use Symbio\OrangeGate\AdminBundle\Admin\Admin as BaseAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ChoiceAdmin extends BaseAdmin
{
    protected $parentAssociationMapping = 'field';

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('translations', 'orangegate_translations', [
                'translation_domain' => $this->translationDomain,
                'label' => false,
                'fields' => [
                    'label' => ['label' => 'label.choice_label']
                ],
            ])
            ->add('value', 'text', ['label' => 'label.choice_value'])
            ->add('priority', null, ['label' => 'label.choice_priority'])
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('label')
            ->add('value')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', 'url', ['label' => 'label.choice_id'])
            ->add('label', 'string', ['label' => 'label.choice_label'])
            ->add('value', 'string', ['label' => 'label.choice_value'])
            ->add('priority', 'int', ['label' => 'label.choice_priority'])
        ;
    }
}