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
use Symbio\OrangeGate\PageBundle\Entity\SitePool;


class FormAdmin extends BaseAdmin
{
    /**
     * @var SitePool
     */
    protected $sitePool;


    /**
     * {@inheritdoc}
     */
    public function __construct($code, $class, $baseControllerName, SitePool $sitePool)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->sitePool = $sitePool;
    }


    /**
     * {@inheritdoc}
     */
    public function getPersistentParameters()
    {
        // todo k cemu je tohle?
        $parameters = parent::getPersistentParameters();

        if ($this->hasRequest()) {
            $parameters['site_id'] = $this->sitePool->getCurrentSite($this->getRequest());
        }

        return $parameters;
    }


    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $object = parent::getNewInstance();
        $object->setSite($this->sitePool->getCurrentSite($this->getRequest()));

        return $object;
    }


    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        // Fields to be shown on create/edit forms
        $formMapper
            ->add('translations', 'orangegate_translations', [
                'translation_domain' => $this->translationDomain,
                'label' => false,
                'fields' => [
                    'name' => ['label' => 'label.form_name'],
                    'description' => ['label' => 'label.form_description'],
                    'submitLabel' => ['label' => 'label.form_submit_label'],
                ],
            ])
            ->add('emailFrom', 'email', ['label' => 'label.form_email_from', 'required' => false])
        ;
    }


    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        // Fields to be shown on filter forms
        $datagridMapper
            ->add('name')
            ->add('site', null, array(
                'show_filter' => false,
            ))

        ;
    }


    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        // Fields to be shown on lists
        $listMapper
            ->addIdentifier('id', 'url', ['label' => 'label.form_id'])
            ->add('name', 'string', ['label' => 'label.form_name'])
            ->add('description', 'textarea', ['label' => 'label.form_description'])
            ->add('_action', 'actions', [
                'label' => 'label.form_actions',
                'actions' => [
                    'edit' => [],
                    'fields' =>  ['template' => 'SymbioOrangeGateFormBundle:FormAdmin:fields_button.html.twig'],
            ]])
        ;
    }
}