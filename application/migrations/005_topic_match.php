<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_topic_match extends CI_Migration
{

    public function up()
    {
                
        $this->db->query('ALTER TABLE `topics` DROP INDEX `gCode`');
        $this->db->query('ALTER TABLE `topics` DROP `gCode`');
        $this->db->query('CREATE UNIQUE INDEX name ON topics(name)');

    }

    public function down()
    {

        $this->db->query('ALTER TABLE `topics` DROP INDEX `name`');
        $this->db->query('ALTER TABLE `topics` ADD `gCode` VARCHAR(1023) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `name`');
        $this->db->query('CREATE UNIQUE INDEX gCode ON topics(gCode)');

    }
}
