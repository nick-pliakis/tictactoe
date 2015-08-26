<?php
/**
 * History class, implements the game history controller
 */
class History extends CI_Controller {
    //Utilizes the history model to get data from the database concerning the game history
    public function __construct() {
        parent::__construct();
        $this->load->model("history_model");
    }
    //Shows all entries in the history table
    public function get_all_history($page = 'history') {
        if (!file_exists(APPPATH . '/views/pages/' . $page . '.php')) {
            // Whoops, we don't have a page for that!
            show_404();
        }

        $data['history'] = $this->history_model->get_history(true);

        $this->load->view('templates/head', $data);
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer', $data);
    }
    //Shows only the latest five entries, displayed on the game sidebar
    public function get_latest_history() {
        $data['latest_history'] = $this->history_model->get_history();
    }
}

?>