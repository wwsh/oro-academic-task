<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\Entity;

use OroAcademy\Bundle\IssueBundle\Entity\IssueResolution;

class IssueResolutionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IssueResolution
     */
    private $item;

    public function setUp()
    {
        $this->item = new IssueResolution();
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
            [ 'name', IssueResolution::RESOLUTION_DUPLICATE ],
            [ 'name', IssueResolution::RESOLUTION_FIXED ],
            [ 'name', IssueResolution::RESOLUTION_INCOMPLETE ],
            [ 'name', IssueResolution::RESOLUTION_INVALID ],
            [ 'name', IssueResolution::RESOLUTION_WONTFIX ],
            [ 'name', IssueResolution::RESOLUTION_WORKSFORME ],
        ];
    }
}