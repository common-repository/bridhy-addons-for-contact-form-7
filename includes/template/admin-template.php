<?php

/**
 * admin template file 
 * all html write here
 */

use \CF7VB\Form\Form_List_Table;
?>

<?php
$listtable = new Form_List_Table();
$listtable->register();
?>
<div class="wrap">
    <div id="app">
        <h2><?php esc_html_e('Contact Forms List', 'cf7vb'); ?></h2>
        <?php
        $listtable->display();
        ?>
    </div>
</div>