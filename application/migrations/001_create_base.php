<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_base extends CI_Migration
{

    public function up()
    {

        ## Create Table aliases
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,
                'auto_increment' => true,
            ),
            'sourceId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 127,
                'null' => false,

            ),
        ));
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("aliases", true);
        $this->db->query('ALTER TABLE  `aliases` ENGINE = InnoDB');
        $this->db->query('CREATE UNIQUE INDEX name ON aliases(name)');

        ## Create Table articles
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
            'gCode' => array(
                'type' => 'VARCHAR',
                'constraint' => 1023,
                'null' => false,

            ),
            'https' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,

            ),
            'articleBaseUrlId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
            'effectiveUrl' => array(
                'type' => 'VARCHAR',
                'constraint' => 1023,
                'null' => false,

            ),
            'title' => array(
                'type' => 'VARCHAR',
                'constraint' => 1023,
                'null' => false,

            ),
            'excerpt' => array(
                'type' => 'VARCHAR',
                'constraint' => 8191,
                'null' => true,

            ),
            'imageBaseUrlId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
            'urlToImage' => array(
                'type' => 'VARCHAR',
                'constraint' => 1023,
                'null' => true,

            ),
            'sourceId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
            'aliasId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
            'guid' => array(
                'type' => 'VARCHAR',
                'constraint' => 1023,
                'null' => true,

            ),
        ));
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("articles", true);
        $this->db->query('ALTER TABLE  `articles` ENGINE = InnoDB');
        $this->db->query('ALTER TABLE articles MODIFY gCode VARCHAR(1023) CHARACTER SET utf8 COLLATE utf8_unicode_ci');
        $this->db->query('CREATE UNIQUE INDEX gCode ON articles(gCode)');
        $this->db->query('CREATE INDEX storyId ON articles(storyId)');
        $this->db->query('CREATE INDEX effectiveUrl ON articles(effectiveUrl)');

        ## Create Table base_urls
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,
                'auto_increment' => true,
            ),
            'url' => array(
                'type' => 'VARCHAR',
                'constraint' => 63,
                'null' => false,

            ),
        ));
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("base_urls", true);
        $this->db->query('ALTER TABLE  `base_urls` ENGINE = InnoDB');
        $this->db->query('CREATE UNIQUE INDEX url ON base_urls(url)');

        ## Create Table categories
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,
                'auto_increment' => true,
            ),
            'gCat' => array(
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => true,

            ),
            'nCat' => array(
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => true,

            ),
            'sequence' => array(
                'type' => 'TINYINT',
                'unsigned' => true,
                'null' => false,

            ),
            'lastUpdate' => array(
                'type' => 'DATETIME',
                'null' => true,

            ),
        ));
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("categories", true);
        $this->db->query('ALTER TABLE  `categories` ENGINE = InnoDB');

        ## Create Table groups
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,
                'auto_increment' => true,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 63,
                'null' => false,

            ),
        ));
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("groups", true);
        $this->db->query('ALTER TABLE  `groups` ENGINE = InnoDB');

        ## Create Table redundant
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,
                'auto_increment' => true,
            ),
            'text' => array(
                'type' => 'VARCHAR',
                'constraint' => 63,
                'null' => false,

            ),
        ));
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("redundant", true);
        $this->db->query('ALTER TABLE  `redundant` ENGINE = InnoDB');

        ## Create Table source_categories
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,
                'auto_increment' => true,
            ),
            'sourceId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
            'categoryId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
        ));
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("source_categories", true);
        $this->db->query('ALTER TABLE  `source_categories` ENGINE = InnoDB');

        ## Create Table sources
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,
                'auto_increment' => true,
            ),
            'groupId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
        ));
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("sources", true);
        $this->db->query('ALTER TABLE  `sources` ENGINE = InnoDB');

        ## Create Table stories
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,
                'auto_increment' => true,
            ),
            'pubDate' => array(
                'type' => 'DATETIME',
                'null' => false,

            ),
            'firstSeen' => array(
                'type' => 'DATETIME',
                'null' => false,

            ),
            'lastSeen' => array(
                'type' => 'DATETIME',
                'null' => false,

            ),
            'precedingPubDate' => array(
                'type' => 'DATETIME',
                'null' => false,

            ),
            'articleCount' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
            'highestArticleCount' => array(
                'type' => 'SMALLINT',
                'unsigned' => true,
                'null' => true,

            ),
            'totalArticleCount' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
            'titleArticleId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
            'excerptArticleId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => true,

            ),
            'imageArticleId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => true,

            ),
            'highestPriority' => array(
                'type' => 'TINYINT',
                'unsigned' => true,
                'null' => false,

            ),
            'lastPriority' => array(
                'type' => 'TINYINT',
                'unsigned' => true,
                'null' => false,

            ),
        ));
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("stories", true);
        $this->db->query('ALTER TABLE  `stories` ENGINE = InnoDB');

        ## Create Table story_categories
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
            'categoryId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
        ));
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("story_categories", true);
        $this->db->query('ALTER TABLE  `story_categories` ENGINE = InnoDB');

        ## Create Table story_relations
        $this->dbforge->add_field(array(
            'storyId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
            'relatedStoryId' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
        ));
        $this->dbforge->add_key("storyId", true);
        $this->dbforge->add_key("relatedStoryId", true);
        $this->dbforge->create_table("story_relations", true);
        $this->db->query('ALTER TABLE  `story_relations` ENGINE = InnoDB');

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

        ## Create Table topics
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,
                'auto_increment' => true,
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 127,
                'null' => false,

            ),
            'lastSeen' => array(
                'type' => 'DATETIME',
                'null' => false,

            ),
            'storyCount' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,

            ),
        ));
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("topics", true);
        $this->db->query('ALTER TABLE  `topics` ENGINE = InnoDB');
        $this->db->query('CREATE UNIQUE INDEX name ON topics(name)');

        $data = array(
            array(
                'gCat' => 'NATION',
                'nCat' => 'general',
                'sequence' => 1,
            ),
            array(
                'gCat' => 'WORLD',
                'nCat' => 'general',
                'sequence' => 5,
            ),
            array(
                'gCat' => 'BUSINESS',
                'nCat' => 'business',
                'sequence' => 3,
            ),
            array(
                'gCat' => 'SCIENCE',
                'nCat' => 'science',
                'sequence' => 7,
            ),
            array(
                'gCat' => 'TECHNOLOGY',
                'nCat' => 'technology',
                'sequence' => 2,
            ),
            array(
                'gCat' => 'HEALTH',
                'nCat' => 'health',
                'sequence' => 4,
            ),
            array(
                'gCat' => 'SPORTS',
                'nCat' => 'sports',
                'sequence' => 6,
            ),
            array(
                'gCat' => 'ENTERTAINMENT',
                'nCat' => 'entertainment',
                'sequence' => 8,
            ),
        );

        $this->db->insert_batch('categories', $data);

    }

    public function down()
    {
        ### Drop table aliases ##
        $this->dbforge->drop_table("aliases", true);
        ### Drop table articles ##
        $this->dbforge->drop_table("articles", true);
        ### Drop table base_urls ##
        $this->dbforge->drop_table("base_urls", true);
        ### Drop table categories ##
        $this->dbforge->drop_table("categories", true);
        ### Drop table groups ##
        $this->dbforge->drop_table("groups", true);
        ### Drop table redundant ##
        $this->dbforge->drop_table("redundant", true);
        ### Drop table source_categories ##
        $this->dbforge->drop_table("source_categories", true);
        ### Drop table sources ##
        $this->dbforge->drop_table("sources", true);
        ### Drop table stories ##
        $this->dbforge->drop_table("stories", true);
        ### Drop table story_categories ##
        $this->dbforge->drop_table("story_categories", true);
        ### Drop table story_relations ##
        $this->dbforge->drop_table("story_relations", true);
        ### Drop table story_topics ##
        $this->dbforge->drop_table("story_topics", true);
        ### Drop table topics ##
        $this->dbforge->drop_table("topics", true);

    }
}
