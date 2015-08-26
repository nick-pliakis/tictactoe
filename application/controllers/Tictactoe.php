<?php
/**
 * The actual game controller, calls all the necessary functions to run the game
 */
class Tictactoe extends CI_Controller {
    //Construct the controller and call the history model to get the five latest
    //entries in the history table
    public function __construct() {
        parent::__construct();
        $this->load->model("history_model");
    }
    //Function that shows the tic-tac-toe game
    public function play($page = 'game') {
        if (!file_exists(APPPATH . '/views/pages/' . $page . '.php')) {
            // Whoops, we don't have a page for that!
            show_404();
        }
        //Load latest five entries from history
        $data['latest_history'] = $this->history_model->get_history();
        //Load the page views
        $this->load->view('templates/head', $data);
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer', $data);
    }
}

?>