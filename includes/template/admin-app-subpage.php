<?php 
    /**
     * admin sub page template file 
     * all html write here
     */
?>
<?php 
    use CF7VB\Form\Form_subPage_Table;
?>

<div class="wrap">
    <div id="icon-users" class="icon32"></div>
    <h2><?php
    $form_post_id = isset( $_GET['fid'] ) ? $_GET['fid']: '';
     echo esc_html(get_the_title( $form_post_id )); 
     ?></h2>

     <?php 
        $subpage = new Form_subPage_Table();
        $subpage->register();
     
     ?>
     <form method="post" action="">
        <?php $subpage->search_box(__( 'Search', 'cf7vb' ), 'search'); ?>
        <?php $subpage->display(); ?>
    </form>
</div>