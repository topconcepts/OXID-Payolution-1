<?php
/**
 * Copyright 2018 Payolution GmbH
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0 [^]
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace TopConcepts\Payolution\Module\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180304170747 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE `oxuser` ADD COLUMN `PAYO_COMPANY_REGISTRATION_NO` VARCHAR (128);
            ALTER TABLE `oxorder` ADD COLUMN `PAYO_COMPANY_REGISTRATION_NO` VARCHAR (128);
            ALTER TABLE `oxorder` ADD COLUMN `PAYO_UNIQUE_ID` VARCHAR(32);
            ALTER TABLE `oxorder` ADD COLUMN `PAYO_INVOICE_SENT` TINYINT(1) NOT NULL DEFAULT 0;
            ALTER TABLE `oxorder` ADD COLUMN `PAYO_STATUS` VARCHAR(20) NOT NULL;');

        $this->addSql('
            ALTER TABLE `oxorder`
            ADD COLUMN `PAYO_LAST_PRECHECK_ID` VARCHAR(32) NULL,
            ADD COLUMN `PAYO_IP` VARCHAR(11) NULL,
            ADD COLUMN `PAYO_PRECHECK_ID` CHAR(32) NULL,
            ADD COLUMN `PAYO_PREAUTH_ID` CHAR(32) NULL,
            ADD COLUMN `PAYO_CAPTURE_ID` CHAR(32) NULL,
            ADD COLUMN `PAYO_REFERENCE_ID` CHAR(14) NULL,
            ADD COLUMN `PAYO_NODEL` TINYINT NOT NULL DEFAULT 0,
            ADD COLUMN `PAYO_REFUND_AVAILABLE` DECIMAL(10,2) NULL,
            ADD COLUMN `PAYO_CAPTURED_PRICE` DECIMAL(10,2) NULL,
            ADD COLUMN `PAYO_PREAUTH_PRICE` DECIMAL(10,2) NULL,
            ADD COLUMN `PAYO_PDF_SENT` TINYINT(1) NOT NULL DEFAULT \'0\'
        ;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
