<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stock_model extends CI_Model
{
    public function get_low_stock()
    {
        $query = "SELECT DISTINCT Material_no, Material_name, Unit, Updated_at, Updated_by,
                    (SUM(CASE WHEN transaction_type = 'IN' THEN Qty ELSE 0 END) - 
                    SUM(CASE WHEN transaction_type = 'OUT' THEN Qty ELSE 0 END)) as Qty
                FROM storage
                WHERE Material_no LIKE '%RW%'
                GROUP BY Material_no, Material_name, Unit
                HAVING Qty <= 25";

        return $this->db->query($query)->result_array();
    }
}