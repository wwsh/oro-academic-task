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

/**
 * Class OroAcademyIssueBundle
 * @package OroAcademy\Bundle\IssueBundle\Migrations
 */
class OroAcademyIssueBundle implements Migration
{
    /**
     * @param Schema   $schema
     * @param QueryBag $queries
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->createOroacademyIssueTable($schema);
        $this->createOroacademyIssuePriorityTable($schema);
        $this->createOroacademyIssueToTagTable($schema);
        $this->createOroacademyIssueToUserTable($schema);
        $this->createOroacademyIssueTypeTable($schema);
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
        $table->addColumn('priority_id', 'integer', ['notnull' => false]);
        $table->addColumn('parent_id', 'integer', ['notnull' => false]);
        $table->addColumn('assignee_user_id', 'integer', ['notnull' => false]);
        $table->addColumn('type_id', 'integer', ['notnull' => false]);
        $table->addColumn('reporter_user_id', 'integer', ['notnull' => false]);
        $table->addColumn('summary', 'string', ['length' => 255]);
        $table->addColumn('code', 'string', ['length' => 255]);
        $table->addColumn('description', 'text', []);
        $table->addColumn('resolution', 'string', ['length' => 255]);
        $table->addColumn('status', 'string', ['length' => 255]);
        $table->addColumn('createdAt', 'datetime', []);
        $table->addColumn('updatedAt', 'datetime', []);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['priority_id'], 'IDX_E7C12BA3497B19F9', []);
        $table->addIndex(['reporter_user_id'], 'IDX_E7C12BA3DF3D6D95', []);
        $table->addIndex(['assignee_user_id'], 'IDX_E7C12BA3BA8D7F59', []);
        $table->addIndex(['parent_id'], 'IDX_E7C12BA3727ACA70', []);
        $table->addIndex(['type_id'], 'IDX_E7C12BA3C54C8C93', []);
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
        $table->setPrimaryKey(['id']);
    }

    /**
     * Create oroacademy_issue_to_tag table
     *
     * @param Schema $schema
     */
    protected function createOroacademyIssueToTagTable(Schema $schema)
    {
        $table = $schema->createTable('oroacademy_issue_to_tag');
        $table->addColumn('issue_id', 'integer', []);
        $table->addColumn('tag_id', 'integer', []);
        $table->setPrimaryKey(['issue_id', 'tag_id']);
        $table->addIndex(['issue_id'], 'IDX_28731CE25E7AA58C', []);
        $table->addIndex(['tag_id'], 'IDX_28731CE2BAD26311', []);
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
        $table->addColumn('id', 'integer', [ 'autoincrement' => true ]);
        $table->addColumn('label', 'string', [ 'length' => 16 ]);
        $table->addColumn('description', 'string', [ 'notnull' => false, 'length' => 255 ]);
        $table->setPrimaryKey([ 'id' ]);
    }
}