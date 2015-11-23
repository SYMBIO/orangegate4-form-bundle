<?php

namespace Symbio\OrangeGate\FormBundle\Block;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symbio\OrangeGate\FormBundle\Service\FormFactory;

class FormBlockService extends BaseBlockService
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var FormFactory
     */
    protected $formFactory;


    /**
     * FormBlockService constructor.
     * @param string $name
     * @param EngineInterface $templating
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @param FormFactory $formFactory
     */
    public function __construct(
        $name,
        EngineInterface $templating,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        FormFactory $formFactory
    ) {
        $this->translator = $translator;
        $this->em = $em;
        $this->formFactory = $formFactory;

        parent::__construct($name, $templating);
    }


    /**
     * @inheritdoc
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();

        // load form model
        if (NULL === $settings['form_id']) {
            // todo find better bad configuration exception
            throw new EntityNotFoundException('Form_id not specified');
        }

        $obj = $this->em->getRepository('SymbioOrangeGateFormBundle:Form')->find($settings['form_id']);
        if (null === $obj) {
            throw new EntityNotFoundException('Cannot find form entity with id ' . $settings['form_id']);
        }

        // create form according to model
        $form = $this->formFactory->createForm($obj);

        // render
        return $this->renderResponse($blockContext->getTemplate(), [
            'block' => $blockContext->getBlock(),
            'settings' => $blockContext->getBlock()->getSettings(),
            'form' => $form->createView(),
            'formId' => $obj->getId(),
        ], $response);
    }


    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->translator->trans('title.form_selector_block', [], 'SymbioOrangeGateFormBundle');
    }


    /**
     * @inheritdoc
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('translations', 'orangegate_translations', [
            'label' => false,
            'locales' => $block->getSite()->getLocales(),
            'fields' => [
                'enabled' => [
                    'field_type' => 'checkbox',
                    'required' => false,
                    'label' => 'Povoleno'
                ],
                'settings' => [
                    'field_type' => 'sonata_type_immutable_array',
                    'label' => false,
                    'keys' => [
                        ['form_id', 'orangegate_form_form', [
                            'label' => $this->translator->trans('label.form_select', [], 'SymbioOrangeGateFormBundle'),
                            'required' => true,
                        ]],
                    ],
                ],
            ],
        ]);
    }


    /**
     * @inheritdoc
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        $errorElement
            ->with('settings.form_id')
            ->assertNotNull(array())
            ->assertNotBlank()
            ->end()
        ;
    }


    /**
     * @inheritdoc
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'form_id' => '',
            'template' => 'SymbioOrangeGateFormBundle:Block:formblock.html.twig',
        ]);
    }
}