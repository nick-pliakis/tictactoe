<?php
/**
 * Class that implements the history model, to retrieve data from the history table
 */
class History_model extends CI_Model {
    //Construct the model and gain access to the database
    public function __construct() {
        $this->load->database();
    }
    //Get history. If the $all variable is true, retrieve every entry.
    //Otherwise, retrieve the most recent five entries.
    public function get_history($all = false) {
        if ($all) {
            $query = $this->db->get("history");
        } else {
            $query = $this->db->order_by("game_date", "desc")->get("history", 5, 0);
        }
        return $query->result_array();        
    }
    //Inserts entries in the history table (not used, since our own method is implemented)
    public function insert_history($data) {
        if (!isset($data)) {
            return false;
        }
        
        $this->db->insert("history", $data);
        return true;
    }
}