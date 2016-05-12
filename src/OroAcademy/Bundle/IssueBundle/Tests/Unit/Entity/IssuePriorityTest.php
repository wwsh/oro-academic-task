<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\Entity;

use OroAcademy\Bundle\IssueBundle\Entity\IssuePriority;

class IssuePriorityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IssuePriority
     */
    private $item;

    public function setUp()
    {
        $this->item = new IssuePriority();
    }

    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $method = 'set' . ucfirst($property);
        $result = $this->item->$method($value);

        $this->assertInstanceOf(get_class($this->item), $result);
        $this->assertEquals($value, $this->item->{'get' . $property}());
    }

    public function settersAndGettersDataProvider()
    {
        return [
            [ 'name', IssuePriority::PRIORITY_HIGH ],
            [ 'name', IssuePriority::PRIORITY_NORMAL ],
            [ 'name', IssuePriority::PRIORITY_LOW ],
            [ 'value', 50 ]
        ];
    }

    /**
     * @dataProvider printDataProvider
     */
    public function testPrintAbility(IssuePriority $issuePriority, $expectedPrint)
    {
        $this->assertEquals($expectedPrint, (string)$issuePriority);
    }

    public function printDataProvider()
    {
        return [
            [ new IssuePriority(IssuePriority::PRIORITY_NORMAL), IssuePriority::PRIORITY_NORMAL ],
            [ new IssuePriority(IssuePriority::PRIORITY_HIGH), IssuePriority::PRIORITY_HIGH ],
            [ new IssuePriority(IssuePriority::PRIORITY_LOW), IssuePriority::PRIORITY_LOW ],
        ];
    }
}