<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_base extends CI_Migration
{

    public function up()
    {
        
        $this->db->query('ALTER TABLE `categories` ADD `sortOrder` TINYINT UNSIGNED NOT NULL AFTER `sequence`');

        $this->db->query('UPDATE `categories` SET `sortOrder` = "1" WHERE `gCat` = "NATION"');
        $this->db->query('UPDATE `categories` SET `sortOrder` = "5" WHERE `gCat` = "WORLD"');
        $this->db->query('UPDATE `categories` SET `sortOrder` = "6" WHERE `gCat` = "BUSINESS"');
        $this->db->query('UPDATE `categories` SET `sortOrder` = "7" WHERE `gCat` = "SCIENCE"');
        $this->db->query('UPDATE `categories` SET `sortOrder` = "8" WHERE `gCat` = "TECHNOLOGY"');
        $this->db->query('UPDATE `categories` SET `sortOrder` = "9" WHERE `gCat` = "HEALTH"');
        $this->db->query('UPDATE `categories` SET `sortOrder` = "10" WHERE `gCat` = "SPORTS"');
        $this->db->query('UPDATE `categories` SET `sortOrder` = "13" WHERE `gCat` = "ENTERTAINMENT"');

        $this->db->query('INSERT INTO `categories` (`id`, `gCat`, `nCat`, `sequence`, `sortOrder`, `lastUpdate`) 
                          VALUES (NULL, "POLITICS", NULL, "0", "2", NULL), 
                                 (NULL, "ECONOMY", NULL, "0", "3", NULL), 
                                 (NULL, "EDUCATION", NULL, "0", "4", NULL), 
                                 (NULL, "FOOTBALL", NULL, "0", "11", NULL), 
                                 (NULL, "BASKETBALL", NULL, "0", "12", NULL)');

        ## Create Table category_keywords
        $this->dbforge->add_field(array(
            'categoryId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,
            ),
            'keyword' => array(
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => false,
            ),
        ));
        $this->dbforge->add_key("categoryId", true);
        $this->dbforge->add_key("keyword", true);
        $this->dbforge->create_table("category_keywords", true);
        $this->db->query('ALTER TABLE  `category_keywords` ENGINE = InnoDB');

    }

    public function down()
    {
        ### Drop table category_keywords ##
        $this->dbforge->drop_table("category_keywords", true);

        $this->db->query('DELETE FROM `categories` WHERE `gCat` = "BASKETBALL"');
        $this->db->query('DELETE FROM `categories` WHERE `gCat` = "FOOTBALL"');
        $this->db->query('DELETE FROM `categories` WHERE `gCat` = "EDUCATION"');
        $this->db->query('DELETE FROM `categories` WHERE `gCat` = "ECONOMY"');
        $this->db->query('DELETE FROM `categories` WHERE `gCat` = "POLITICS"');

        $this->db->query('ALTER TABLE `categories` DROP `sortOrder`');

    }
}
