<?php
/**
 * Created by PhpStorm.
 * User: jiri.bazant
 * Date: 16.11.15
 * Time: 10:32
 */

namespace Symbio\OrangeGate\FormBundle\Tests\Entity;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    public function choiceProvider()
    {
        return [
            ['select', true],
            ['radio', true],
            ['checkboxes', true],
            ['text', false],
            ['textarea', false],
            ['captcha', false]
        ];
    }

    /**
     * @param string $value
     * @param bool $isChoice
     * @dataProvider choiceProvider
     */
    public function testIsChoice($value, $isChoice)
    {
        $field = new \Symbio\OrangeGate\FormBundle\Entity\Field(1, 'test', $value);
        $this->assertEquals($isChoice, $field->isChoice());
    }


    /**
     * @param $value
     * @depends testIsChoice
     * @dataProvider choiceProvider
     */
    public function testType($value)
    {
        $field = new \Symbio\OrangeGate\FormBundle\Entity\Field(1, 'test', $value);

        if ($field->isChoice()) {
            $this->assertEquals('choice', $field->getFormFieldType());
            $this->assertArraySubset(
                [
                    'label' => 'test',
                    'required' => false,
                    'choices' => [],
                ],
                $field->getFormFieldParams()
            );
        } else {
            $this->assertEquals($value, $field->getFormFieldType());
            $this->assertArraySubset(
                [
                    'label' => 'test',
                    'required' => false,
                ],
                $field->getFormFieldParams()
            );
        }
    }

    public function testParams()
    {
        $field = new \Symbio\OrangeGate\FormBundle\Entity\Field(1, 'test', 'text', true);

        $this->assertEquals(
            ['label' => 'test', 'required' => true],
            $field->getFormFieldParams()
        );

        $field->setRequired(false);
        $field->setType('radio');
        $field->setChoices([
            new \Symbio\OrangeGate\FormBundle\Entity\Choice('label1', 'value1', 1, $field),
            new \Symbio\OrangeGate\FormBundle\Entity\Choice('label2', 'value2', 2, $field),
        ]);
        $this->assertEquals(
            [
                'label' => 'test',
                'required' => false,
                'choices' => ['value1' => 'label1', 'value2' => 'label2'],
                'expanded' => true
            ],
            $field->getFormFieldParams()
        );

        $field->setType('checkboxes');
        $this->assertEquals(
            [
                'label' => 'test',
                'required' => false,
                'choices' => ['value1' => 'label1', 'value2' => 'label2'],
                'expanded' => true,
                'multiple' => true,
            ],
            $field->getFormFieldParams()
        );

        $field->setType('select');
        $this->assertEquals(
            [
                'label' => 'test',
                'required' => false,
                'choices' => ['value1' => 'label1', 'value2' => 'label2'],
            ],
            $field->getFormFieldParams()
        );
    }


    public function testChoicesArrayPriority()
    {
        $field = new \Symbio\OrangeGate\FormBundle\Entity\Field(1, '', 'select', true);
        $field->setChoices([
            new \Symbio\OrangeGate\FormBundle\Entity\Choice('label2', 'value2', 2, $field),
            new \Symbio\OrangeGate\FormBundle\Entity\Choice('label1', 'value1', 1, $field),
        ]);

        $params = $field->getChoicesArray();
        $this->assertEquals(['value1' => 'label1', 'value2' => 'label2'], $params);
        // make sure that keys are really in right order (by priority)
        $this->assertEquals(['value1', 'value2'], array_keys($params));
    }
}
