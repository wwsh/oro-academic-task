<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\NoteBundle\Migration\Extension\NoteExtension;
use Oro\Bundle\NoteBundle\Migration\Extension\NoteExtensionAwareInterface;

/**
 * Class OroAcademyIssueBundle
 * @package OroAcademy\Bundle\IssueBundle\Migrations
 */
class OroAcademyIssueBundle implements Migration, NoteExtensionAwareInterface
{
    /** @var NoteExtension */
    protected $noteExtension;

    /**
     * {@inheritdoc}
     */
    public function setNoteExtension(NoteExtension $noteExtension)
    {
        $this->noteExtension = $noteExtension;
    }

    /**
     * @param Schema   $schema
     * @param QueryBag $queries
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->createOroacademyIssueTable($schema);
        $this->createOroacademyIssuePriorityTable($schema);
        $this->createOroacademyIssueResolutionTable($schema);
        $this->createOroacademyIssueToUserTable($schema);
        $this->createOroacademyIssueTypeTable($schema);
        $this->createOroacademyIssueToIssueTable($schema);
        $this->addOroacademyIssueForeignKeys($schema);
        $this->addOroacademyIssueToUserForeignKeys($schema);
        $this->noteExtension->addNoteAssociation($schema, 'oroacademy_issue');
    }

    /**
     * Create oroacademy_issue table
     *
     * @param Schema $schema
     */
    protected function createOroacademyIssueTable(Schema $schema)
    {
        $table = $schema->createTable('oroacademy_issue');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('workflow_item_id', 'integer', ['notnull' => false]);
        $table->addColumn('resolution_id', 'integer', ['notnull' => false]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('priority_id', 'integer', ['notnull' => false]);
        $table->addColumn('workflow_step_id', 'integer', ['notnull' => false]);
        $table->addColumn('parent_id', 'integer', ['notnull' => false]);
        $table->addColumn('assignee_user_id', 'integer', ['notnull' => false]);
        $table->addColumn('type_id', 'integer', ['notnull' => false]);
        $table->addColumn('reporter_user_id', 'integer', ['notnull' => false]);
        $table->addColumn('summary', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('code', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('description', 'text', ['notnull' => false]);
        $table->addColumn('createdAt', 'datetime', []);
        $table->addColumn('updatedAt', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['priority_id'], 'IDX_E7C12BA3497B19F9', []);
        $table->addIndex(['reporter_user_id'], 'IDX_E7C12BA3DF3D6D95', []);
        $table->addIndex(['assignee_user_id'], 'IDX_E7C12BA3BA8D7F59', []);
        $table->addIndex(['parent_id'], 'IDX_E7C12BA3727ACA70', []);
        $table->addIndex(['type_id'], 'IDX_E7C12BA3C54C8C93', []);
        $table->addIndex(['resolution_id'], 'IDX_E7C12BA312A1C43A', []);
        $table->addIndex(['workflow_item_id'], 'IDX_E7C12BA31023C4EE', []);
        $table->addIndex(['workflow_step_id'], 'IDX_E7C12BA371FE882C', []);
        $table->addIndex(['organization_id'], 'IDX_E7C12BA332C8A3DE', []);
    }

    /**
     * Create oroacademy_issue_priority table
     *
     * @param Schema $schema
     */
    protected function createOroacademyIssuePriorityTable(Schema $schema)
    {
        $table = $schema->createTable('oroacademy_issue_priority');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('value', 'integer', []);
        $table->addColumn('label', 'string', ['length' => 255]);
        $table->setPrimaryKey(['id']);
    }

    /**
     * Create oroacademy_issue_resolution table
     *
     * @param Schema $schema
     */
    protected function createOroacademyIssueResolutionTable(Schema $schema)
    {
        $table = $schema->createTable('oroacademy_issue_resolution');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'text', ['notnull' => false]);
        $table->addColumn('label', 'string', ['length' => 255]);
        $table->setPrimaryKey(['id']);
    }

    /**
     * Create oroacademy_issue_to_issue table
     *
     * @param Schema $schema
     */
    protected function createOroacademyIssueToIssueTable(Schema $schema)
    {
        $table = $schema->createTable('oroacademy_issue_to_issue');
        $table->addColumn('issue_id', 'integer', []);
        $table->addColumn('related_issue_id', 'integer', []);
        $table->setPrimaryKey(['issue_id', 'related_issue_id']);
        $table->addIndex(['issue_id'], 'IDX_2F4F28425E7AA58C', []);
        $table->addIndex(['related_issue_id'], 'IDX_2F4F2842F8F9EB21', []);
    }

    /**
     * Create oroacademy_issue_to_user table
     *
     * @param Schema $schema
     */
    protected function createOroacademyIssueToUserTable(Schema $schema)
    {
        $table = $schema->createTable('oroacademy_issue_to_user');
        $table->addColumn('issue_id', 'integer', []);
        $table->addColumn('user_id', 'integer', []);
        $table->setPrimaryKey(['issue_id', 'user_id']);
        $table->addIndex(['issue_id'], 'IDX_B70D7D2C5E7AA58C', []);
        $table->addIndex(['user_id'], 'IDX_B70D7D2CA76ED395', []);
    }

    /**
     * Create oroacademy_issue_type table
     *
     * @param Schema $schema
     */
    protected function createOroacademyIssueTypeTable(Schema $schema)
    {
        $table = $schema->createTable('oroacademy_issue_type');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('label', 'string', ['length' => 255]);
        $table->addColumn('description', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('name', 'string', ['length' => 16]);
        $table->setPrimaryKey(['id']);
    }

    /**
     * Add oroacademy_issue foreign keys.
     *
     * @param Schema $schema
     */
    protected function addOroacademyIssueForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('oroacademy_issue');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_workflow_item'),
            ['workflow_item_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oroacademy_issue_resolution'),
            ['resolution_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oroacademy_issue_priority'),
            ['priority_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_workflow_step'),
            ['workflow_step_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oroacademy_issue'),
            ['parent_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['assignee_user_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oroacademy_issue_type'),
            ['type_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['reporter_user_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }

    /**
     * Add oroacademy_issue_to_issue foreign keys.
     *
     * @param Schema $schema
     */
    protected function addOroacademyIssueToIssueForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('oroacademy_issue_to_issue');
        $table->addForeignKeyConstraint(
            $schema->getTable('oroacademy_issue'),
            ['issue_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oroacademy_issue'),
            ['related_issue_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

    /**
     * Add oroacademy_issue_to_user foreign keys.
     *
     * @param Schema $schema
     */
    protected function addOroacademyIssueToUserForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('oroacademy_issue_to_user');
        $table->addForeignKeyConstraint(
            $schema->getTable('oroacademy_issue'),
            ['issue_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['user_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

}