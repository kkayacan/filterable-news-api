<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Buchin\GoogleImageGrabber\GoogleImageGrabber;

class Welcome extends CI_Controller
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *         http://example.com/index.php/welcome
     *    - or -
     *         http://example.com/index.php/welcome/index
     *    - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
        $this->load->view('welcome_message');
    }
/*
    public function find_image()
    {
        $keyword = $_GET['keyword'];
        if ($keyword != "") {
            $images = GoogleImageGrabber::grab($keyword);
            if ($images) {
                foreach ($images as $image) {
                    echo '<img src="' . $image['url'] . '"/><br>';
                    echo '<p>Width: ' . $image['width'] . '</p>';
                    echo '<p>Height: ' . $image['height'] . '</p><br><br>';
                }
            }
        }
    }
*/
}
