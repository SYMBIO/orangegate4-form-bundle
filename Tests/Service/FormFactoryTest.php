<?php

use Symbio\OrangeGate\FormBundle\Entity\Field;
use Symbio\OrangeGate\FormBundle\Entity\Form;
use Symbio\OrangeGate\FormBundle\Entity\Choice;
use Symbio\OrangeGate\FormBundle\Service\FormFactory;
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 16.11.15
 * Time: 9:23
 */
class FormFactoryTest extends PHPUnit_Framework_TestCase
{
    private function getFormFactoryStub($builderMock)
    {
        $formFactory = $this->getMockBuilder('\Symfony\Component\Form\FormFactory')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $formFactory
            ->method('createBuilder')
            ->willReturn($builderMock)
        ;

        return $formFactory;
    }

    private function getTranslatorStub()
    {
        $translatorStub = $this->getMockBuilder('\Symfony\Component\Translation\LoggingTranslator')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $translatorStub->method('trans')->will($this->returnArgument(0));

        return $translatorStub;
    }

    private function getFormBuilderMock()
    {
        $formBuilder = $this->getMockBuilder('\Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $formBuilder->expects($this->once())->method('getForm')->willReturn('Some unique string. Yea!');

        return $formBuilder;
    }


    public function testCreateFormSimple()
    {
        $formBuilder = $this->getFormBuilderMock();
        $formBuilder->expects($this->exactly(3))
            ->method('add')
            ->withConsecutive(
                ['field_1', 'text', ['label' => 'Item 1', 'required' => false]],
                ['field_2', 'textarea', ['label' => 'Item 2', 'required' => true]],
                ['submit', 'submit', ['label' => 'form.button_submit']]
            );

        $formEntity = new Form(1, 'TestSimple', null, 1);
        $formEntity->setFields([
            new Field(1, 'Item 1', 'text', false, 1),
            new Field(2, 'Item 2', 'textarea', true, 2),
        ]);

        $factory = new FormFactory($this->getFormFactoryStub($formBuilder), $this->getTranslatorStub());

        $this->assertEquals('Some unique string. Yea!', $factory->createForm($formEntity));
    }


    public function testCreateFormPriority()
    {
        $formBuilder = $this->GetFormBuilderMock();
        $formBuilder->expects($this->exactly(3))
            ->method('add')
            ->withConsecutive(
                ['field_2', 'textarea', ['label' => 'Item 2', 'required' => true]],
                ['field_3', 'textarea', ['label' => 'Item 3', 'required' => true]],
                ['field_1', 'textarea', ['label' => 'Item 1', 'required' => true]],
                ['submit', 'submit', ['label' => 'Odeslat']]
            );

        $formEntity = new Form(1, 'TestSimple', null, 1);
        $formEntity->setFields([
            new Field(1, 'Item 1', 'textarea', true, 5),
            new Field(2, 'Item 2', 'textarea', true, 1),
            new Field(3, 'Item 3', 'textarea', true, 1),
        ]);

        $factory = new FormFactory($this->getFormFactoryStub($formBuilder), $this->getTranslatorStub());

        // a bit fake test, but we want to test that createForm returns result of FormBuilder->getForm();
        $this->assertEquals('Some unique string. Yea!', $factory->createForm($formEntity));
    }


    public function testCreateFormValidators()
    {
        $this->markTestIncomplete('Not implemented yet');
    }

    public function testHoneyPot()
    {
        $this->markTestIncomplete('Not implemented yet');
    }

    public function testGetFormData()
    {
        // todo test multiple value field
        $factory = new FormFactory($this->getFormFactoryStub(null), $this->getTranslatorStub());

        $fieldChoice = new Field(3, 'Field 3 choice Label', 'radio');
        $fieldChoice->setChoices([
            new Choice('Choice 1', 1),
            new Choice('Choice 2', 2),
        ]);
        $formEntity = new Form(1, 'TestSimple', null, 1);
        $formEntity->setFields([
            new Field(1, 'Item 1 Label', 'textarea'),
            new Field(2, 'Item 2 Label', 'textarea'),
            $fieldChoice,
        ]);

        $formStub = $this->getMockBuilder('\Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $formStub->method('getData')->willReturn([
            'field_1' => 'data 1',
            'field_2' => 2,
            'field_3' => 2,
        ]);

        $this->assertEquals(
            [
                'field_1' => ['label' => 'Item 1 Label', 'value' => 'data 1'],
                'field_2' => ['label' => 'Item 2 Label', 'value' => 2],
                'field_3' => ['label' => 'Field 3 choice Label', 'value' => 'Choice 2'],
            ],
            $factory->getFormData($formEntity, $formStub)
        );
    }
}
