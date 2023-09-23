<?php
/*
Plugin Name: Woocommerce Show All Order Notes
Version: 1.0
Author: MAR
*/

add_action('admin_menu', 'wc_get_order_notes_admin_menu');

function wc_get_order_notes_admin_menu() {
  add_menu_page('Show All Order Notes', 'Show All Order Notes', 'manage_options', 'wc-get-all-order-notes', 'wc_get_order_notes_admin_page');
}

function wc_get_order_notes_admin_page() {
global $wpdb;
	
  $results = $wpdb->get_results("SELECT comment_post_ID AS order_id, comment_date AS note_date, GROUP_CONCAT( CASE WHEN comment_type = 'order_note' THEN CONCAT(comment_content, ' (', comment_date, ')') WHEN comment_type = 'order_status' THEN CONCAT('Order Status Update: ', comment_content, ' (', comment_date, ')') END SEPARATOR '|' ) AS order_updates_and_notes FROM wp_comments WHERE comment_type IN ('order_note', 'order_status') GROUP BY comment_post_ID, note_date ORDER BY comment_post_ID DESC, note_date ASC LIMIT 10;");


  echo '<h3> This will display 10 latest results, Use download option to export the data. </h3>';
  echo '<table class="table table-bordered" border="1">';
  echo '<thead><tr><th>Order ID</th><th>Note added Date and Time</th> <th> Order Notes</th></tr></thead>';
  foreach ($results as $result) {
    echo '<tr>
	<td>' . $result->order_id . '</td>
	<td>' . $result->note_date . '</td>
	<td>' . $result->order_updates_and_notes . '</td>
	</tr>';
  }
  echo '</table>';

  echo '<a href="' . admin_url('admin-ajax.php?action=download_100_csv') . '" class="button button-primary">Export 100 Rows</a>';
  echo '<a href="' . admin_url('admin-ajax.php?action=download_all_csv') . '" class="button button-primary">Export All Rows</a>';
}

add_action('wp_ajax_download_100_csv', 'download_100_csv');
add_action('wp_ajax_download_all_csv', 'download_all_csv');

function download_100_csv() {
    global $wpdb;
    $results = $wpdb->get_results("SELECT comment_post_ID AS order_id, comment_date AS note_date, GROUP_CONCAT( CASE WHEN comment_type = 'order_note' THEN CONCAT(comment_content, ' (', comment_date, ')') WHEN comment_type = 'order_status' THEN CONCAT('Order Status Update: ', comment_content, ' (', comment_date, ')') END SEPARATOR '|' ) AS order_updates_and_notes FROM wp_comments WHERE comment_type IN ('order_note', 'order_status') GROUP BY comment_post_ID, note_date ORDER BY comment_post_ID DESC, note_date ASC LIMIT 100;");
      $csv_file = fopen('php://output', 'w');
  
    fputcsv($csv_file, array('Order ID', 'Date Time', 'Order Notes'));
      foreach ($results as $result) {
      fputcsv($csv_file, array($result->order_id, $result->note_date,$result->order_updates_and_notes ));
    }
      fclose($csv_file);
      $date = new DateTime();

  header('Content-Type: application/csv');
  $filename = date('Y-m-d_H-i-s') . '.csv';
  header('Content-Disposition: attachment; filename=' . $filename);
  
    readfile('php://output');
}
function download_all_csv() {
    global $wpdb;
    $results = $wpdb->get_results("SELECT comment_post_ID AS order_id, comment_date AS note_date, GROUP_CONCAT( CASE WHEN comment_type = 'order_note' THEN CONCAT(comment_content, ' (', comment_date, ')') WHEN comment_type = 'order_status' THEN CONCAT('Order Status Update: ', comment_content, ' (', comment_date, ')') END SEPARATOR '|' ) AS order_updates_and_notes FROM wp_comments WHERE comment_type IN ('order_note', 'order_status') GROUP BY comment_post_ID, note_date ORDER BY comment_post_ID DESC, note_date ASC;");
      $csv_file = fopen('php://output', 'w');
  
    fputcsv($csv_file, array('Order ID', 'Date Time', 'Order Notes'));
      foreach ($results as $result) {
      fputcsv($csv_file, array($result->order_id, $result->note_date,$result->order_updates_and_notes ));
    }
      fclose($csv_file);
      $date = new DateTime();

  header('Content-Type: application/csv');
  $filename = date('Y-m-d_H-i-s') . '.csv';
  header('Content-Disposition: attachment; filename=' . $filename);
  
    readfile('php://output');
}


?>

