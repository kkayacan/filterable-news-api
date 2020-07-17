<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_top_duration extends CI_Migration
{

    public function up()
    {
        
        $this->db->query('ALTER TABLE `stories` ADD `topFirstSeen` DATETIME NULL AFTER `lastSeen`, ADD `topLastSeen` DATETIME NULL AFTER `topFirstSeen`');

    }

    public function down()
    {
     
        $this->db->query('ALTER TABLE `stories` DROP `topFirstSeen`, DROP `topLastSeen`;');

    }
}
