<?php

declare(strict_types=1);

/*
 * This file is part of Contao Party Bundle.
 *
 * (c) Max Pawellek
 *
 * @license LGPL-3.0-or-later
 */

namespace Echtermax\PartyBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class Version101 extends AbstractMigration
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        return !$schemaManager->tablesExist(['tl_party_response']);
    }
    
    public function run(): MigrationResult
    {
        $this->connection->executeStatement("
            CREATE TABLE IF NOT EXISTS `tl_party_response` (
              `id` int(10) unsigned NOT NULL auto_increment,
              `tstamp` int(10) unsigned NOT NULL default '0',
              `pid` int(10) unsigned NOT NULL default '0',
              `member_id` int(10) unsigned NOT NULL default '0',
              `response` varchar(10) NOT NULL default '',
              PRIMARY KEY  (`id`),
              KEY `pid` (`pid`),
              KEY `member_id` (`member_id`),
              UNIQUE KEY `party_member` (`pid`, `member_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        return new MigrationResult(
            true,
            'Tabelle "tl_party_response" wurde erstellt.'
        );
    }
}
