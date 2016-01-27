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

class RecipientAdmin extends BaseAdmin
{
    protected $parentAssociationMapping = 'form';

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', ['label' => 'label.recipient_name'])
            ->add('email', 'text', ['label' => 'label.recipient_choice'])
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('email')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', 'url', ['label' => 'label.choice_id'])
            ->add('name', 'text', ['label' => 'label.recipient_name'])
            ->add('email', 'text', ['label' => 'label.recipient_choice'])
        ;
    }
}