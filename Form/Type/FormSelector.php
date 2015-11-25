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
use Symfony\Component\HttpFoundation\Request;
use Symbio\OrangeGate\PageBundle\Entity\SitePool;

class FormSelector extends AbstractType
{
    /**
     * @var Doctrine\ORM\EntityRepository
     */
    private $repository;

    /**
     * @var \Sonata\PageBundle\Model\Site;
     */
    private $site;


    /**
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function __construct($doctrine, SitePool $sitePool, Request $request) {
        $this->repository = $doctrine->getManager()->getRepository('SymbioOrangeGateFormBundle:Form');
        $this->site = $sitePool->getCurrentSite($request);
    }


    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $entities = $this->repository->findBy(['site' => $this->site], ['name' => 'ASC', 'id' => 'ASC']);

        $choices = [];
        foreach ($entities as $entity) {
            $choices[$entity->getId()] = $entity->getName();
        }
        $resolver->setDefaults(['choices' => $choices]);
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
        return 'orangegate_form_form';
    }
}