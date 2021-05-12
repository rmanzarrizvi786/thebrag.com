<?php
$paging = $this->paging;
$show_paging = $paging->get_page_count() > 1;
?>

<div class="grid-toolbar row">
    <div class="clearfix col s12<?php if ( $show_paging ): ?> m6 l4<?php endif; ?>">
        <select class="bulk-actions-select browser-default left" autocomplete="off">
            <option value="" selected="selected"><?php _e( '- Bulk actions -', 'ari-stream-quiz' ); ?></option>
            <option value="bulk_delete"><?php _e( 'Delete', 'ari-stream-quiz' ); ?></option>
        </select>
        &nbsp;<button class="btn btn-cmd blue waves-effect waves-light btn-bulk-apply"><?php _e( 'Apply', 'ari-stream-quiz' ); ?></button>
    </div>
    <?php
    if ( $show_paging ):
        $paging->render();
    endif;
    ?>
</div>