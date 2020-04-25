<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_base extends CI_Migration {

	public function up() {

		## Create Table aliases
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'sourceId' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => 63,
				'null' => FALSE,

			),
		));
		$this->dbforge->add_key("id",true);
		$this->dbforge->create_table("aliases", TRUE);
		$this->db->query('ALTER TABLE  `aliases` ENGINE = InnoDB');

		## Create Table articles
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'storyId' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
			'gCode' => array(
				'type' => 'VARCHAR',
				'constraint' => 511,
				'null' => FALSE,

			),
			'https' => array(
				'type' => 'TINYINT',
				'constraint' => 1,
				'null' => FALSE,

			),
			'effectiveUrl' => array(
				'type' => 'VARCHAR',
				'constraint' => 1023,
				'null' => FALSE,

			),
			'title' => array(
				'type' => 'VARCHAR',
				'constraint' => 1023,
				'null' => FALSE,

			),
			'excerpt' => array(
				'type' => 'VARCHAR',
				'constraint' => 8191,
				'null' => TRUE,

			),
			'urlToImage' => array(
				'type' => 'VARCHAR',
				'constraint' => 1023,
				'null' => TRUE,

			),
			'sourceId' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
			'aliasId' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
		));
		$this->dbforge->add_key("id",true);
		$this->dbforge->create_table("articles", TRUE);
		$this->db->query('ALTER TABLE  `articles` ENGINE = InnoDB');

		## Create Table categories
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'gCat' => array(
				'type' => 'VARCHAR',
				'constraint' => 15,
				'null' => TRUE,

			),
			'nCat' => array(
				'type' => 'VARCHAR',
				'constraint' => 15,
				'null' => TRUE,

			),
			'lastUpdate' => array(
				'type' => 'DATETIME',
				'null' => TRUE,

			),
		));
		$this->dbforge->add_key("id",true);
		$this->dbforge->create_table("categories", TRUE);
		$this->db->query('ALTER TABLE  `categories` ENGINE = InnoDB');

		## Create Table groups
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => 63,
				'null' => FALSE,

			),
		));
		$this->dbforge->add_key("id",true);
		$this->dbforge->create_table("groups", TRUE);
		$this->db->query('ALTER TABLE  `groups` ENGINE = InnoDB');

		## Create Table redundant
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'text' => array(
				'type' => 'VARCHAR',
				'constraint' => 63,
				'null' => FALSE,

			),
		));
		$this->dbforge->add_key("id",true);
		$this->dbforge->create_table("redundant", TRUE);
		$this->db->query('ALTER TABLE  `redundant` ENGINE = InnoDB');

		## Create Table related_stories
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'storyId' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
			'followUpStroyId' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
		));
		$this->dbforge->add_key("id",true);
		$this->dbforge->create_table("related_stories", TRUE);
		$this->db->query('ALTER TABLE  `related_stories` ENGINE = InnoDB');

		## Create Table source_categories
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'sourceId' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
			'categoryId' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
		));
		$this->dbforge->add_key("id",true);
		$this->dbforge->create_table("source_categories", TRUE);
		$this->db->query('ALTER TABLE  `source_categories` ENGINE = InnoDB');

		## Create Table sources
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'groupId' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
		));
		$this->dbforge->add_key("id",true);
		$this->dbforge->create_table("sources", TRUE);
		$this->db->query('ALTER TABLE  `sources` ENGINE = InnoDB');

		## Create Table stories
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'pubDate' => array(
				'type' => 'DATETIME',
				'null' => FALSE,

			),
			'firstSeen' => array(
				'type' => 'DATETIME',
				'null' => FALSE,

			),
			'lastSeen' => array(
				'type' => 'DATETIME',
				'null' => FALSE,

			),
			'precedingPubDate' => array(
				'type' => 'DATETIME',
				'null' => FALSE,

			),
			'articleCount' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
			'totalArticleCount' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
			'titleArticleId' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
			'excerptArticleId' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
		));
		$this->dbforge->add_key("id",true);
		$this->dbforge->create_table("stories", TRUE);
		$this->db->query('ALTER TABLE  `stories` ENGINE = InnoDB');

		## Create Table story_categories
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'storyId' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
			'categoryId' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
		));
		$this->dbforge->add_key("id",true);
		$this->dbforge->create_table("story_categories", TRUE);
		$this->db->query('ALTER TABLE  `story_categories` ENGINE = InnoDB');

		## Create Table story_topics
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'storyId' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
			'topicId' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
		));
		$this->dbforge->add_key("id",true);
		$this->dbforge->create_table("story_topics", TRUE);
		$this->db->query('ALTER TABLE  `story_topics` ENGINE = InnoDB');

		## Create Table topics
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => 127,
				'null' => FALSE,

			),
			'lastSeen' => array(
				'type' => 'DATETIME',
				'null' => FALSE,

			),
			'storyCount' => array(
				'type' => 'INT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,

			),
		));
		$this->dbforge->add_key("id",true);
		$this->dbforge->create_table("topics", TRUE);
		$this->db->query('ALTER TABLE  `topics` ENGINE = InnoDB');
	 }

	public function down()	{
		### Drop table aliases ##
		$this->dbforge->drop_table("aliases", TRUE);
		### Drop table articles ##
		$this->dbforge->drop_table("articles", TRUE);
		### Drop table categories ##
		$this->dbforge->drop_table("categories", TRUE);
		### Drop table groups ##
		$this->dbforge->drop_table("groups", TRUE);
		### Drop table redundant ##
		$this->dbforge->drop_table("redundant", TRUE);
		### Drop table related_stories ##
		$this->dbforge->drop_table("related_stories", TRUE);
		### Drop table source_categories ##
		$this->dbforge->drop_table("source_categories", TRUE);
		### Drop table sources ##
		$this->dbforge->drop_table("sources", TRUE);
		### Drop table stories ##
		$this->dbforge->drop_table("stories", TRUE);
		### Drop table story_categories ##
		$this->dbforge->drop_table("story_categories", TRUE);
		### Drop table story_topics ##
		$this->dbforge->drop_table("story_topics", TRUE);
		### Drop table topics ##
		$this->dbforge->drop_table("topics", TRUE);

	}
}