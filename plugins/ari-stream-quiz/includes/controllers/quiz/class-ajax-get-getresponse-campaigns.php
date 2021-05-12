<?php
namespace Ari_Stream_Quiz\Controllers\Quiz;

use Ari\Controllers\Ajax as Ajax_Controller;
use Ari\Utils\Request as Request;
use Ari_Stream_Quiz\Helpers\Helper as Helper;

class Ajax_Get_Getresponse_Campaigns extends Ajax_Controller {
    protected function process_request() {
        if ( $this->options->nopriv || ( ! current_user_can( 'edit_others_posts' ) && ! current_user_can( 'edit_posts' ) ) )
            return null;

        $reload = (bool)Request::get_var( 'reload' );

        $campaigns = Helper::get_getresponse_campaigns( $reload );

        return $campaigns;
    }
}
