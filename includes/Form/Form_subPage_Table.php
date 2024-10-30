<?php

/**
 * Form_subPage_Table
 * @package cf7vb
 */

namespace CF7VB\Form;

use WP_List_Table;

class Form_subPage_Table extends WP_List_Table
{

    private $form_post_id;
    private $column_titles = array();

    public function __construct()
    {
        parent::__construct(
            array(
                'singular' => 'contact_form',
                'plural'   => 'contact_forms',
                'ajax'     => false
            )
        );
    }

    /**
     * Prepare the items for the table to process
     *
     * @return void
     */
    public function register()
    {
        $this->form_post_id = isset($_GET['fid']) ? (int) $_GET['fid'] : 0;
        $search = empty($_REQUEST['s']) ? false : esc_sql($_REQUEST['s']);

        global $wpdb;
        $table_name = $wpdb->prefix . 'cf7vb_forms';
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->table_data();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
        $this->prepare_items();
    }

    public function prepare_items()
    {
        // ... (other preparations)
        global $wpdb;
        $table_name = $wpdb->prefix . 'cf7vb_forms';

        $per_page = $this->get_items_per_page($table_name, 20); // Adjust the default per page here

        $current_page = $this->get_pagenum();
        $total_items  = count($this->items);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ));

        $this->items = array_slice($this->items, ($current_page - 1) * $per_page, $per_page);
    }


    // get_columns
    public function get_columns()
    {
        $columns = array();
        $form_post_id = $this->form_post_id;
        global $wpdb;
        $table_name = $wpdb->prefix . 'cf7vb_forms';
        $results = $wpdb->get_results("SELECT * FROM $table_name WHERE form_post_id = $form_post_id ORDER BY form_id DESC LIMIT 1");
        $first_row = isset($results[0]) ? unserialize($results[0]->form_data) : '';

        if (!empty($first_row)) {
            foreach ($first_row as $key => $value) {
                $key = esc_html($key);
                $key_val = str_replace(array('your-', 'cf7vb_file'), '', $key);
                $key_val = str_replace(array('_', '-'), ' ', $key_val);
                $key_val = preg_replace('/^\d+|\d+$/', '', $key_val);
                $columns[$key] = ucwords($key_val);
                $this->column_titles[] = $key_val;
            }
            $columns['form-date'] = 'Date';
        }
        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return array
     */
    public function get_hidden_columns()
    {
        return array('form_id');
    }

    /**
     * Define the sortable columns
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        return array('form-date' => array('form-date', true));
    }

    /**
     * Get the table data
     *
     * @return array
     */
    private function table_data()
    {
        $data = array();
        global $wpdb;
        $search = empty($_REQUEST['s']) ? false : esc_sql($_REQUEST['s']);
        $table_name = $wpdb->prefix . 'cf7vb_forms';
        $form_post_id = $this->form_post_id;

        if (!empty($search)) {
            $results = $wpdb->get_results("SELECT * FROM $table_name WHERE form_data LIKE '%$search%' AND form_post_id = '$form_post_id' ORDER BY form_id ASC", OBJECT);
        } else {
            $results = $wpdb->get_results("SELECT * FROM $table_name WHERE form_post_id = $form_post_id ORDER BY form_id ASC", OBJECT);
        }

        foreach ($results as $result) {
            $form_value = isset($result) ? unserialize($result->form_data) : '';
            $link = "<b>%s</b>";

            $fid = $result->form_post_id;
            $form_values['form_id'] = $result->form_id;

            foreach ($this->column_titles as $col_title) {
                $form_value[$col_title] = isset($form_value[$col_title]) ? $form_value[$col_title] : '';
            }

            foreach ($form_value as $key => $value) {
                $value = esc_html($value);
                $form_values[$key] = (strlen($value) > 100) ? substr($value, 0, 100) . '...' : $value;
                $form_values[$key] = sprintf($link, $form_values[$key]);
            }

            $form_values['form-date'] = sprintf($link, $result->form_date);
            $data[] = $form_values;
        }

        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  array $item Data
     * @param  string $column_name - Current column name
     *
     * @return mixed
     */
    public function column_default($item, $column_name)
    {
        return isset($item[$column_name]) ? $item[$column_name] : '';
    }
}
