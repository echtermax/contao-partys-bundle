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

class Version100 extends AbstractMigration
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        // Die Migration soll nur ausgeführt werden, wenn die Tabelle noch nicht existiert
        return !$schemaManager->tablesExist(['tl_party']);
    }

    public function run(): MigrationResult
    {
        $this->connection->executeStatement("
            CREATE TABLE IF NOT EXISTS `tl_party` (
              `id` int(10) unsigned NOT NULL auto_increment,
              `tstamp` int(10) unsigned NOT NULL default '0',
              `title` varchar(255) NOT NULL default '',
              `description` text NULL,
              `date` varchar(10) NOT NULL default '',
              `location` varchar(255) NOT NULL default '',
              `published` char(1) NOT NULL default '',
              PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        return new MigrationResult(
            true,
            'Tabelle "tl_party" wurde erstellt.'
        );
    }
}
