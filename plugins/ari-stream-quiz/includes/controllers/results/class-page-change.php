<?php
namespace Ari_Stream_Quiz\Controllers\Results;

use Ari\Controllers\Controller as Controller;
use Ari\Utils\Response as Response;
use Ari\Utils\Request as Request;
use Ari_Stream_Quiz\Helpers\Helper as Helper;

class Page_Change extends Controller {
    public function execute() {
        $model = $this->model();

        if ( Request::exists( 'results_page' ) ) {
            $page_num = Request::get_var( 'results_page', 0, 'num' );

            if ( $page_num >= 0 ) {
                $filter = $model->get_state( 'filter' );
                $filter['page_num'] = $page_num;
                $model->set_state( 'filter', $filter );
            }
        }

        Response::redirect(
            Helper::build_url(
                array(
                    'page' => 'ari-stream-quiz-results',

                    'filter' => $model->encoded_filter_state(),
                )
            )
        );
    }
}
