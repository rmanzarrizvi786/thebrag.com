<?php
namespace Ari_Stream_Quiz\Controllers\Quiz;

use Ari\Utils\Response as Response;
use Ari_Stream_Quiz\Helpers\Helper as Helper;

class Save_Preview extends Save {
    protected function saved_successfully( $entity ) {
        $url_params = array();
        $post_id = $entity->post_id;
        $post_link = $post_id > 0 ? get_permalink( $post_id ) : false;

        if ( $post_link ) {
            $url_params['preview'] = $post_id;
        }

        parent::saved_successfully( $entity, $url_params );
    }
}
