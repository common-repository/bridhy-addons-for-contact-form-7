<?php

/**
 * admin template file 
 * all html write here
 */

use \CF7VB\Form\Form_List_Builder_Table;

?>

<?php
$listtable = new Form_List_Builder_Table();
$listtable->register();
?>
<div class="wrap">
    <div id="app">
        <h2><?php esc_html_e('All Contact Forms', 'cf7vb'); ?></h2>
        <?php
        $listtable->display();
        ?>
    </div>
</div>