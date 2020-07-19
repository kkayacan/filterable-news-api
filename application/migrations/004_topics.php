<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_topics extends CI_Migration
{

    public function up()
    {
        
        $this->db->query('ALTER TABLE `topics` ADD `gCode` VARCHAR(1023) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `name`');
        $this->db->query('ALTER TABLE `topics` DROP INDEX `name`');
        $this->db->query('CREATE UNIQUE INDEX gCode ON topics(gCode)');
        
        ### Drop table story_topics ##
        $this->dbforge->drop_table("story_topics", true);

        $this->dbforge->add_field(array(
            'storyId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
            'topicId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
        ));
        $this->dbforge->add_key("storyId", true);
        $this->dbforge->add_key("topicId", true);
        $this->dbforge->create_table("story_topics", true);
        $this->db->query('ALTER TABLE  `story_topics` ENGINE = InnoDB');

    }

    public function down()
    {

        $this->dbforge->drop_table("story_topics", true);

        ## Create Table story_topics
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,
                'auto_increment' => true,
            ),
            'storyId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
            'topicId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
        ));
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("story_topics", true);
        $this->db->query('ALTER TABLE  `story_topics` ENGINE = InnoDB');
        
        $this->db->query('CREATE UNIQUE INDEX name ON topics(name)');
        $this->db->query('ALTER TABLE `topics` DROP INDEX `gCode`');
        $this->db->query('ALTER TABLE `topics` DROP `gCode`');

    }
}
