<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migrate extends CI_Controller
{
    public function index()
    {
        // load migration library
        $this->load->library('migration');

        if (!$this->migration->current()) {
            echo 'Error' . $this->migration->error_string();
        } else {
            echo 'Migrations ran successfully!';
        }
    }

    public function make_base()
    {
        $this->load->library('ci_migrations_generator/Sqltoci');
        // All Tables:
        $this->sqltoci->generate();
        //Single Table:
        //$this->sqltoci->generate('table');
    }
}
