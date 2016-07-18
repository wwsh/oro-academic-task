<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Unit\DependencyInjection;

use Oro\Bundle\TestFrameworkBundle\Test\DependencyInjection\ExtensionTestCase;
use OroAcademy\Bundle\IssueBundle\DependencyInjection\OroAcademyIssueExtension;

class OroAcademyIssueExtensionTest extends ExtensionTestCase
{
    /**
     * Test Extension
     */
    public function testExtension()
    {
        $extension = new OroAcademyIssueExtension();

        $this->loadExtension($extension);

        $expectedParameters = [
            'issue_entity',
        ];

        $expectedServices = [
            'oroacademy_entity_association_helper.api',
            'oroacademy_issue_handler',
            'oroacademy_form_helper.subtask'
        ];

        $this->assertParametersLoaded($expectedParameters);
        $this->assertDefinitionsLoaded($expectedServices);
    }
}
