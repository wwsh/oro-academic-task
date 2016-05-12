<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\Entity;

use OroAcademy\Bundle\IssueBundle\Entity\IssueType;

class IssueTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IssueType
     */
    private $item;

    public function setUp()
    {
        $this->item = new IssueType();
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
            [ 'name', IssueType::TYPE_BUG ],
            [ 'name', IssueType::TYPE_TASK ],
            [ 'name', IssueType::TYPE_SUBTASK ],
            [ 'name', IssueType::TYPE_STORY ],
            [ 'description', 'Example Description' ],
        ];
    }

    /**
     * @dataProvider printDataProvider
     */
    public function testPrintAbility(IssueType $issueType, $expectedPrint)
    {
        $this->assertEquals($expectedPrint, (string)$issueType);
    }

    public function printDataProvider()
    {
        return [
            [ new IssueType(IssueType::TYPE_TASK), IssueType::TYPE_TASK ],
            [ new IssueType(IssueType::TYPE_STORY), IssueType::TYPE_STORY ],
            [ new IssueType(IssueType::TYPE_SUBTASK), IssueType::TYPE_SUBTASK ],
            [ new IssueType(IssueType::TYPE_BUG), IssueType::TYPE_BUG ],
        ];
    }

}