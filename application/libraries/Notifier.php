<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notifier
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('email');
        $this->CI->load->model('Stock_model');
    }

    public function send_low_stock_notification()
    {
        $lowStocks = $this->CI->Stock_model->get_low_stock();

        if (empty($lowStocks)) {
            return false;
        }

        $message = "<h3>Low Stock Notification!</h3>";
        $message .= "<p>Beberapa raw material sudah mencapai level minimum:</p>";
        $message .= "<table border='1' cellpadding='6' cellspacing='0' style='border-collapse: collapse;'>
                <thead>
                    <tr style='background:#f2f2f2'>
                        <th>Material No</th>
                        <th>Material Name</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Last Update</th>
                        <th>Updated By</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($lowStocks as $row) {
            $message .= "<tr>
                            <td>{$row['Material_no']}</td>
                            <td>{$row['Material_name']}</td>
                            <td>{$row['Qty']}</td>
                            <td>{$row['Unit']}</td>
                            <td>{$row['Updated_at']}</td>
                            <td>{$row['Updated_by']}</td>
                        </tr>";
        }

        $message .= "</tbody></table>";

        $recipients = [
            "kevinmarthakusuma03@gmail.com"
            // "marketing@ckc.com",
            // "management@ckc.com",
            // "admin1@ckc.com"
        ];

        $this->CI->email->from("inventorynotifckc@gmail.com", "Inventory Notification");
        $this->CI->email->to($recipients);
        $this->CI->email->subject("Low Stock Alert - CKC Inventory");
        $this->CI->email->message($message);

        return $this->CI->email->send();
    }
}
