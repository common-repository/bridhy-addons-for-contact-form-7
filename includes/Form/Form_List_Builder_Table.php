<?php

/**
 * form list table
 * @package cf7vb
 */

namespace CF7VB\Form;

use WP_List_Table;
use WP_Query;

class Form_List_Builder_Table extends WP_List_Table
{
    public function register()
    {

        $columns = $this->get_columns();
        $hidden  = $this->get_hidden_columns();
        $data   = $this->table_data();
        $count_forms = wp_count_posts('wpcf7_contact_form');
        $perPage     = 10;
        $currentPage = $this->get_pagenum();
        $offset      = ($currentPage - 1) * $perPage;
        $totalItems  = $count_forms->publish;
        $args = array(
            'post_type'      => 'wpcf7_contact_form',
            'posts_per_page' => $perPage,
            'offset'         => $offset,
            'total_pages' => ceil($totalItems / $perPage)
        );
        $forms_query = new WP_Query($args);

        $totalItems  = $count_forms->publish;
        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ));
        $this->_column_headers = array($columns, $hidden);
        $this->items = $data;
    }

    /**
     * create 2 columns
     */
    public function get_columns()
    {
        $columns = array(
            'name' => __('Name', 'contact-form-cfdb7'),
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        global $wpdb;
        $data = array();
        $page = $this->get_pagenum();
        $page = $page - 1;
        $start = $page * 1;

        $args = array(
            'post_type'      => 'wpcf7_contact_form',
            'order'          => 'ASC',
            'posts_per_page' => 10,
            'offset'         => $start,
        );

        $the_query = new WP_Query($args);

        while ($the_query->have_posts()) : $the_query->the_post();
            $form_post_id = get_the_id();

            // Check if the post is a tab (modify the condition as needed)
            if (strpos(get_the_title(), 'tab') !== false) {
                continue; // Skip counting tabs
            }

            $title = get_the_title();
            $edit_link = admin_url("admin.php?page=wpcf7&post=$form_post_id&action=edit");
            $link_with_edit = "<a class='row-title' href='$edit_link'>%s</a>";
            $data_value['name'] = sprintf($link_with_edit, $title);

            $data[] = $data_value;
        endwhile;

        return $data;
    }



    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default($item, $column_name)
    {
        return $item[$column_name];
    }
}
