<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 16.11.15
 * Time: 9:23
 */

namespace Symbio\OrangeGate\FormBundle\Tests\Service;

use Symbio\OrangeGate\FormBundle\Entity\Field;
use Symbio\OrangeGate\FormBundle\Entity\Form;
use Symbio\OrangeGate\FormBundle\Entity\Choice;
use Symbio\OrangeGate\FormBundle\Service\FormFactory;
use Symfony\Component\Validator\Constraints;

class FormFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates Symfony Form Factory stub object
     * @param $builderMock
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
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

    /**
     * Creates Symfony Translator Stub
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getTranslatorStub()
    {
        $translatorStub = $this->getMockBuilder('\Symfony\Component\Translation\LoggingTranslator')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $translatorStub->method('trans')->will($this->returnArgument(0));

        return $translatorStub;
    }

    /**
     * Creates Symfony Form Builder
     * @param bool $addExpectation default true
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFormBuilderMock($addExpectation = true)
    {
        $formBuilder = $this->getMockBuilder('\Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if ($addExpectation) {
            $formBuilder->expects($this->once())->method('getForm')->willReturn('Some unique string. Yea!');
        }

        return $formBuilder;
    }


    /**
     * Tests simple form creation
     */
    public function testCreateFormSimple()
    {
        $formBuilder = $this->getFormBuilderMock();
        $formBuilder->expects($this->atLeast(3)) // it will be called 4 times cause of honeypot field
            ->method('add')
            ->withConsecutive(
                ['field_1', 'text', ['label' => 'Item 1', 'required' => false]],
                ['field_2', 'textarea', ['label' => 'Item 2', 'required' => false]],
                ['submit', 'submit', ['label' => 'Odeslat']]
            );

        $formEntity = new Form(1, 'TestSimple', null, 1, 'Odeslat');
        $formEntity->setFields([
            new Field(1, 'Item 1', 'text', false, 1),
            new Field(2, 'Item 2', 'textarea', false, 2),
        ]);

        $factory = new FormFactory($this->getFormFactoryStub($formBuilder), $this->getTranslatorStub());

        // a bit fake test, but we want to test that createForm returns result of FormBuilder->getForm();
        $this->assertEquals('Some unique string. Yea!', $factory->createForm($formEntity));
    }


    /**
     * Tests that form fields are ordered by priority
     */
    public function testCreateFormPriority()
    {
        $formBuilder = $this->GetFormBuilderMock();
        $formBuilder->expects($this->atLeast(3))
            ->method('add')
            ->withConsecutive(
                ['field_2', 'textarea', ['label' => 'Item 2', 'required' => false]],
                ['field_3', 'textarea', ['label' => 'Item 3', 'required' => false]],
                ['field_1', 'textarea', ['label' => 'Item 1', 'required' => false]]
            );

        $formEntity = new Form(1, 'TestSimple', null, 1, 'Odeslat');
        $formEntity->setFields([
            new Field(1, 'Item 1', 'textarea', false, 5),
            new Field(2, 'Item 2', 'textarea', false, 1),
            new Field(3, 'Item 3', 'textarea', false, 1),
        ]);

        $factory = new FormFactory($this->getFormFactoryStub($formBuilder), $this->getTranslatorStub());

        $factory->createForm($formEntity);
    }


    /**
     * Tests that validators are created according to Field settings
     */
    public function testCreateFormValidators()
    {
        $formBuilder = $this->GetFormBuilderMock();
        $formBuilder->expects($this->atLeast(3))
            ->method('add')
            ->withConsecutive(
                ['field_1', 'text', ['label' => 'Item 1', 'required' => false]],
                ['field_2', 'text', ['label' => 'Item 2', 'required' => true, 'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Type(['type' => 'numeric',]),
                ]]],
                ['field_3', 'text', ['label' => 'Item 3', 'required' => false, 'constraints' => [new Constraints\Email([
                    'message' => 'neni email'
                ])]]],
                ['field_4', 'text', ['label' => 'Item 4', 'required' => false, 'constraints' => [new Constraints\Regex([
                    'pattern' => '/^ahoj$/',
                    'message' => 'neni ahoj',
                ])]]]
            );

        $formEntity = new Form(1, 'TestSimple', null, 1);
        $formEntity->setFields([
            new Field(1, 'Item 1', 'text', false, 1, 'none'),
            new Field(2, 'Item 2', 'text',  true, 2, 'number'),
            new Field(3, 'Item 3', 'text', false, 3, 'email', 'neni email'),
            new Field(4, 'Item 4', 'text', false, 4, 'regexp', 'neni ahoj', '/^ahoj$/')
        ]);

        $factory = new FormFactory($this->getFormFactoryStub($formBuilder), $this->getTranslatorStub());

        $factory->createForm($formEntity);
    }

    /**
     * Tests that honey pod field is created automaticaly
     */
    public function testHoneyPot()
    {
        $formBuilder = $this->GetFormBuilderMock();
        $formBuilder->expects($this->exactly(3))
            ->method('add')
            ->withConsecutive(
                ['field_1', 'text', ['label' => 'Item 1', 'required' => false]],
                ['submit', 'submit', ['label' => 'Odeslat']],
                ['email', 'email', ['label' => 'form.label_honeypot', 'required' => false, 'constraints' => [new Constraints\Blank()]]]
            );

        $formEntity = new Form(1, 'TestSimple', null, 1, 'Odeslat');
        $formEntity->setFields([
            new Field(1, 'Item 1', 'text', false, 1, 'none'),
        ]);

        $factory = new FormFactory($this->getFormFactoryStub($formBuilder), $this->getTranslatorStub());

        $factory->createForm($formEntity);
    }

    /**
     * Test FormFactory form data transformation
     */
    public function testGetFormData()
    {
        // create form entity
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

        // create form stub
        $formStub = $this->getMockBuilder('\Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $formStub->method('getData')->willReturn([
            'field_1' => 'data 1',
            'field_2' => 2,
            'field_3' => 2,
        ]);

        // create factory and test response
        $factory = new FormFactory($this->getFormFactoryStub(null), $this->getTranslatorStub());

        $this->assertEquals(
            [
                'field_1' => ['label' => 'Item 1 Label', 'value' => 'data 1'],
                'field_2' => ['label' => 'Item 2 Label', 'value' => 2],
                'field_3' => ['label' => 'Field 3 choice Label', 'value' => 'Choice 2'],
            ],
            $factory->getFormData($formEntity, $formStub)
        );
    }

    /**
     * @expectedException \Symbio\OrangeGate\FormBundle\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Unknown validation type: invalid_validation_name
     */
    public function testInvalidValidationTypeThrowsException()
    {
        $formBuilder = $this->GetFormBuilderMock(false);

        $formEntity = new Form(1, 'TestSimple', null, 1, 'Odeslat');
        $formEntity->setFields([
            new Field(1, 'Item 1', 'text', false, 1, 'invalid_validation_name'),
        ]);

        $factory = new FormFactory($this->getFormFactoryStub($formBuilder), $this->getTranslatorStub());
        $factory->createForm($formEntity);
    }
}
