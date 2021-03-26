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
class Version20180304170739 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE IF NOT EXISTS `payo_logs` (
              `OXID`     char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
              `OXSHOPID` CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
              `ADDED_AT` datetime DEFAULT NULL,
              `ORDER_ID` char(32) DEFAULT '',
              `ORDER_NO` varchar(250) DEFAULT NULL,
              `REQUEST`  text NOT NULL,
              `RESPONSE` text
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Log if Payolution API requests';
        ");

        $this->addSql("
            CREATE TABLE IF NOT EXISTS `payo_history` (
              `OXID`           CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
              `OXSHOPID`       CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
              `ORDER_ID`       CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
              `STATUS`         CHAR(15)                  NOT NULL,
              `HISTORY_VALUES` TEXT,
              `ADDED_AT`       DATETIME                  NOT NULL,
              `ADDED_BY`       CHAR(32)                  NOT NULL
            )
              ENGINE =MyISAM
              DEFAULT CHARSET =latin1
              COMMENT ='History of Payolution orders changes';
        ");

        $this->addSql("
            CREATE TABLE IF NOT EXISTS `payo_returnamount` (
              `OXID`        CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
              `OXSHOPID` CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
              `POINVOICEID` CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
              `POAMOUNT`    DOUBLE                    NOT NULL DEFAULT '0',
              `PODATE`      DATETIME                  NOT NULL DEFAULT '0000-00-00 00:00:00',
              PRIMARY KEY (`OXID`),
              KEY `POINVOICEID` (`POINVOICEID`)
            )
              ENGINE =MyISAM
              DEFAULT CHARSET =latin1;
        ");

        $this->addSql("
           CREATE TABLE IF NOT EXISTS `payo_ordershipments` (
              `oxid` varchar(250) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
              `oxshopid` CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
              `item_id` varchar(250) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
              `amount` float DEFAULT NULL,
              PRIMARY KEY (`oxid`,`item_id`)
            );
        ");

        $this->addLanguages();

        $this->addSql('
            REPLACE INTO `oxcontents` (`OXID`, `OXLOADID`, `OXSHOPID`, `OXSNIPPET`, `OXTYPE`, `OXACTIVE`, `OXACTIVE_1`, `OXPOSITION`, `OXTITLE`, `OXCONTENT`, `OXTITLE_1`, `OXCONTENT_1`, `OXACTIVE_2`, `OXTITLE_2`, `OXCONTENT_2`, `OXACTIVE_3`, `OXTITLE_3`, `OXCONTENT_3`, `OXCATID`, `OXFOLDER`, `OXTERMVERSION`) VALUES(\'fb79c1eab6a393606f3dc738e5f7786f\',\'payolutionPdfEmailPlain\',\'oxbaseshop\',\'1\',\'0\',\'1\',\'1\',\'\',\'Bestellung [#oxordernr] im Onlineshop [#sShopUrl] - [#oxorderdate-date] [#oxorderdate-time]\',\'[{oxmultilang ident=\"PAYOLUTION_GENERAL_EMAIL\"}]: [{$order->oxorder__oxbillemail->value}][{oxmultilang ident=\"PAYOLUTION_GENERAL_BILLADDRESS\"}][{if $order->oxorder__oxbillcompany->value }][{oxmultilang ident=\"GENERAL_COMPANY\"}] [{$order->oxorder__oxbillcompany->value }][{/if}][{if $order->oxorder__oxbilladdinfo->value }][{$order->oxorder__oxbilladdinfo->value }][{/if}][{$order->oxorder__oxbillsal->value|oxmultilangsal}] [{$order->oxorder__oxbillfname->value }] [{$order->oxorder__oxbilllname->value }][{$order->oxorder__oxbillstreet->value }] [{$order->oxorder__oxbillstreetnr->value }][{$order->oxorder__oxbillstateid->value}][{$order->oxorder__oxbillzip->value }] [{$order->oxorder__oxbillcity->value }][{$order->oxorder__oxbillcountry->value }][{if $order->oxorder__oxbillcompany->value && $order->oxorder__oxbillustid->value }]    [{oxmultilang ident=\"PAYOLUTION_ORDER_OVERVIEW_VATID\" }] [{$order->oxorder__oxbillustid->value}][{/if}][{oxmultilang ident=\"PAYOLUTION_GENERAL_DELIVERYADDRESS\" }]:[{if $order->oxorder__oxdelcompany->value}]Firma [{$order->oxorder__oxdelcompany->value}][{/if}][{if $order->oxorder__oxdeladdinfo->value}][{$order->oxorder__oxdeladdinfo->value }][{/if}][{$order->oxorder__oxdelsal->value|oxmultilangsal }] [{$order->oxorder__oxdelfname->value }] [{$order->oxorder__oxdellname->value}][{$order->oxorder__oxdelstreet->value}] [{$order->oxorder__oxdelstreetnr->value}][{$order->oxorder__oxdelstateid->value}][{$order->oxorder__oxdelzip->value}] [{$order->oxorder__oxdelcity->value}][{$order->oxorder__oxdelcountry->value}][{oxmultilang ident=\"PAYOLUTION_TOTAL_SUM\" }] [{$order->oxorder__oxtotalordersum->value}] [{$order->oxorder__oxcurrency->value}] [{oxmultilang ident=\"PAYOLUTION_REFERENCE_ID\" }]: [{$order->oxorder__payo_reference_id->value}]\',\'Bestellung [#oxordernr] im Onlineshop [#sShopUrl] - [#oxorderdate-date] [#oxorderdate-time]\',\'[{oxmultilang ident=\"PAYOLUTION_GENERAL_EMAIL\"}]: [{$order->oxorder__oxbillemail->value}][{oxmultilang ident=\"PAYOLUTION_GENERAL_BILLADDRESS\"}][{if $order->oxorder__oxbillcompany->value }][{oxmultilang ident=\"GENERAL_COMPANY\"}] [{$order->oxorder__oxbillcompany->value }][{/if}][{if $order->oxorder__oxbilladdinfo->value }][{$order->oxorder__oxbilladdinfo->value }][{/if}][{$order->oxorder__oxbillsal->value|oxmultilangsal}] [{$order->oxorder__oxbillfname->value }] [{$order->oxorder__oxbilllname->value }][{$order->oxorder__oxbillstreet->value }] [{$order->oxorder__oxbillstreetnr->value }][{$order->oxorder__oxbillstateid->value}][{$order->oxorder__oxbillzip->value }] [{$order->oxorder__oxbillcity->value }][{$order->oxorder__oxbillcountry->value }][{if $order->oxorder__oxbillcompany->value && $order->oxorder__oxbillustid->value }]    [{oxmultilang ident=\"PAYOLUTION_ORDER_OVERVIEW_VATID\" }] [{$order->oxorder__oxbillustid->value}][{/if}][{oxmultilang ident=\"PAYOLUTION_GENERAL_DELIVERYADDRESS\" }]:[{if $order->oxorder__oxdelcompany->value}]Firma [{$order->oxorder__oxdelcompany->value}][{/if}][{if $order->oxorder__oxdeladdinfo->value}][{$order->oxorder__oxdeladdinfo->value }][{/if}][{$order->oxorder__oxdelsal->value|oxmultilangsal }] [{$order->oxorder__oxdelfname->value }] [{$order->oxorder__oxdellname->value}][{$order->oxorder__oxdelstreet->value}] [{$order->oxorder__oxdelstreetnr->value}][{$order->oxorder__oxdelstateid->value}][{$order->oxorder__oxdelzip->value}] [{$order->oxorder__oxdelcity->value}][{$order->oxorder__oxdelcountry->value}][{oxmultilang ident=\"PAYOLUTION_TOTAL_SUM\" }] [{$order->oxorder__oxtotalordersum->value}] [{$order->oxorder__oxcurrency->value}] [{oxmultilang ident=\"PAYOLUTION_REFERENCE_ID\" }]: [{$order->oxorder__payo_reference_id->value}]\',\'0\',\'\',\'\',\'0\',\'\',\'\',\'30e44ab83fdee7564.23264141\',\'\',\'\');
            REPLACE INTO `oxcontents` (`OXID`, `OXLOADID`, `OXSHOPID`, `OXSNIPPET`, `OXTYPE`, `OXACTIVE`, `OXACTIVE_1`, `OXPOSITION`, `OXTITLE`, `OXCONTENT`, `OXTITLE_1`, `OXCONTENT_1`, `OXACTIVE_2`, `OXTITLE_2`, `OXCONTENT_2`, `OXACTIVE_3`, `OXTITLE_3`, `OXCONTENT_3`, `OXCATID`, `OXFOLDER`, `OXTERMVERSION`) VALUES(\'e7c3141304042eb551ccd731145597fc\',\'payolutionPdfEmailHtml\',\'oxbaseshop\',\'1\',\'0\',\'1\',\'1\',\'\',\'Bestellung [#oxordernr] im Onlineshop [#sShopUrl] - [#oxorderdate-date] [#oxorderdate-time]\',\'[{oxmultilang ident=\"PAYOLUTION_GENERAL_EMAIL\"}]: [{$order->oxorder__oxbillemail->value}]<br><br>[{oxmultilang ident=\"PAYOLUTION_GENERAL_BILLADDRESS\"}]<br><br>[{if $order->oxorder__oxbillcompany->value }][{oxmultilang ident=\"GENERAL_COMPANY\"}] [{$order->oxorder__oxbillcompany->value }]<br>[{/if}][{if $order->oxorder__oxbilladdinfo->value }][{$order->oxorder__oxbilladdinfo->value }]<br>[{/if}][{$order->oxorder__oxbillsal->value|oxmultilangsal}] [{$order->oxorder__oxbillfname->value }] [{$order->oxorder__oxbilllname->value }]<br>[{$order->oxorder__oxbillstreet->value }] [{$order->oxorder__oxbillstreetnr->value }]<br>[{$order->oxorder__oxbillstateid->value}]<br>[{$order->oxorder__oxbillzip->value }] [{$order->oxorder__oxbillcity->value }]<br>[{$order->oxorder__oxbillcountry->value }]<br>[{if $order->oxorder__oxbillcompany->value && $order->oxorder__oxbillustid->value }]    [{oxmultilang ident=\"PAYOLUTION_ORDER_OVERVIEW_VATID\" }] [{$order->oxorder__oxbillustid->value}]<br>[{/if}]<br>[{oxmultilang ident=\"PAYOLUTION_GENERAL_DELIVERYADDRESS\" }]:<br><br>[{if $order->oxorder__oxdelcompany->value}]Firma [{$order->oxorder__oxdelcompany->value}]<br>[{/if}][{if $order->oxorder__oxdeladdinfo->value}][{$order->oxorder__oxdeladdinfo->value }]<br>[{/if}][{$order->oxorder__oxdelsal->value|oxmultilangsal }] [{$order->oxorder__oxdelfname->value }] [{$order->oxorder__oxdellname->value}]<br>[{$order->oxorder__oxdelstreet->value}] [{$order->oxorder__oxdelstreetnr->value}]<br>[{$order->oxorder__oxdelstateid->value}]<br>[{$order->oxorder__oxdelzip->value}] [{$order->oxorder__oxdelcity->value}]<br>[{$order->oxorder__oxdelcountry->value}]<br><br><br>[{oxmultilang ident=\"PAYOLUTION_TOTAL_SUM\" }] [{$order->oxorder__oxtotalordersum->value}] [{$order->oxorder__oxcurrency->value}]<br><br>[{oxmultilang ident=\"PAYOLUTION_REFERENCE_ID\" }]: [{$order->oxorder__payo_reference_id->value}]\',\'\',\'[{oxmultilang ident=\"PAYOLUTION_GENERAL_EMAIL\"}]: [{$order->oxorder__oxbillemail->value}]<br><br>[{oxmultilang ident=\"PAYOLUTION_GENERAL_BILLADDRESS\"}]<br><br>[{if $order->oxorder__oxbillcompany->value }][{oxmultilang ident=\"GENERAL_COMPANY\"}] [{$order->oxorder__oxbillcompany->value }]<br>[{/if}][{if $order->oxorder__oxbilladdinfo->value }][{$order->oxorder__oxbilladdinfo->value }]<br>[{/if}][{$order->oxorder__oxbillsal->value|oxmultilangsal}] [{$order->oxorder__oxbillfname->value }] [{$order->oxorder__oxbilllname->value }]<br>[{$order->oxorder__oxbillstreet->value }] [{$order->oxorder__oxbillstreetnr->value }]<br>[{$order->oxorder__oxbillstateid->value}]<br>[{$order->oxorder__oxbillzip->value }] [{$order->oxorder__oxbillcity->value }]<br>[{$order->oxorder__oxbillcountry->value }]<br>[{if $order->oxorder__oxbillcompany->value && $order->oxorder__oxbillustid->value }]    [{oxmultilang ident=\"PAYOLUTION_ORDER_OVERVIEW_VATID\" }] [{$order->oxorder__oxbillustid->value}]<br>[{/if}]<br>[{oxmultilang ident=\"PAYOLUTION_GENERAL_DELIVERYADDRESS\" }]:<br><br>[{if $order->oxorder__oxdelcompany->value}]Firma [{$order->oxorder__oxdelcompany->value}]<br>[{/if}][{if $order->oxorder__oxdeladdinfo->value}][{$order->oxorder__oxdeladdinfo->value }]<br>[{/if}][{$order->oxorder__oxdelsal->value|oxmultilangsal }] [{$order->oxorder__oxdelfname->value }] [{$order->oxorder__oxdellname->value}]<br>[{$order->oxorder__oxdelstreet->value}] [{$order->oxorder__oxdelstreetnr->value}]<br>[{$order->oxorder__oxdelstateid->value}]<br>[{$order->oxorder__oxdelzip->value}] [{$order->oxorder__oxdelcity->value}]<br>[{$order->oxorder__oxdelcountry->value}]<br><br><br>[{oxmultilang ident=\"PAYOLUTION_TOTAL_SUM\" }] [{$order->oxorder__oxtotalordersum->value}] [{$order->oxorder__oxcurrency->value}]<br><br>[{oxmultilang ident=\"PAYOLUTION_REFERENCE_ID\" }]: [{$order->oxorder__payo_reference_id->value}]\',\'1\',\'\',\'[{oxmultilang ident=\"PAYOLUTION_GENERAL_EMAIL\"}]: [{$order->oxorder__oxbillemail->value}]<br><br>[{oxmultilang ident=\"PAYOLUTION_GENERAL_BILLADDRESS\"}]<br><br>[{if $order->oxorder__oxbillcompany->value }][{oxmultilang ident=\"GENERAL_COMPANY\"}] [{$order->oxorder__oxbillcompany->value }]<br>[{/if}][{if $order->oxorder__oxbilladdinfo->value }][{$order->oxorder__oxbilladdinfo->value }]<br>[{/if}][{$order->oxorder__oxbillsal->value|oxmultilangsal}] [{$order->oxorder__oxbillfname->value }] [{$order->oxorder__oxbilllname->value }]<br>[{$order->oxorder__oxbillstreet->value }] [{$order->oxorder__oxbillstreetnr->value }]<br>[{$order->oxorder__oxbillstateid->value}]<br>[{$order->oxorder__oxbillzip->value }] [{$order->oxorder__oxbillcity->value }]<br>[{$order->oxorder__oxbillcountry->value }]<br>[{if $order->oxorder__oxbillcompany->value && $order->oxorder__oxbillustid->value }]    [{oxmultilang ident=\"PAYOLUTION_ORDER_OVERVIEW_VATID\" }] [{$order->oxorder__oxbillustid->value}]<br>[{/if}]<br>[{oxmultilang ident=\"PAYOLUTION_GENERAL_DELIVERYADDRESS\" }]:<br><br>[{if $order->oxorder__oxdelcompany->value}]Firma [{$order->oxorder__oxdelcompany->value}]<br>[{/if}][{if $order->oxorder__oxdeladdinfo->value}][{$order->oxorder__oxdeladdinfo->value }]<br>[{/if}][{$order->oxorder__oxdelsal->value|oxmultilangsal }] [{$order->oxorder__oxdelfname->value }] [{$order->oxorder__oxdellname->value}]<br>[{$order->oxorder__oxdelstreet->value}] [{$order->oxorder__oxdelstreetnr->value}]<br>[{$order->oxorder__oxdelstateid->value}]<br>[{$order->oxorder__oxdelzip->value}] [{$order->oxorder__oxdelcity->value}]<br>[{$order->oxorder__oxdelcountry->value}]<br><br><br>[{oxmultilang ident=\"PAYOLUTION_TOTAL_SUM\" }] [{$order->oxorder__oxtotalordersum->value}] [{$order->oxorder__oxcurrency->value}]<br><br>[{oxmultilang ident=\"PAYOLUTION_REFERENCE_ID\" }]: [{$order->oxorder__payo_reference_id->value}]\',\'1\',\'\',\'\',\'30e44ab83fdee7564.23264141\',\'\',\'\');
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }

    /**
     * @return void
     */
    private function addLanguages()
    {
        require_once(dirname(__FILE__) . '/../../../../../bootstrap.php');

        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $this->addSql('
            REPLACE INTO oxconfig (OXID, OXSHOPID, OXMODULE, OXVARNAME, OXVARTYPE, OXVARVALUE)
            VALUES (\'a54a3a3800bdd392b59109280c472d10\',"' . $config->getActiveShop()->getId() . '",\'\',\'aPayolutionLanguage\',\'aarr\',ENCODE(\'a:7:{s:2:"DA";s:2:"27";s:2:"DE";s:2:"28";s:2:"EN";s:2:"31";s:2:"FI";s:2:"37";s:2:"NB";s:2:"97";s:2:"NL";s:3:"101";s:2:"SV";s:3:"138";}\', "' . $config->getConfigParam('sConfigKey') . '"));
        ');
    }
}
