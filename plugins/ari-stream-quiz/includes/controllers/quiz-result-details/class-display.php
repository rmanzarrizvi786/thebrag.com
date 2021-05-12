<?php
namespace Ari_Stream_Quiz\Controllers\Quiz_Result_Details;

use Ari\Controllers\Display as Display_Controller;
use Ari\Utils\Response as Response;
use Ari_Stream_Quiz\Helpers\Helper as Helper;

class Display extends Display_Controller {
    public function display( $tmpl = null ) {
        $model = $this->model();
        $data = $model->data();

        if ( empty( $data['quiz'] ) ) {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-results',
                    ),
                    array(
                        'id',
                    )
                )
            );
        }

        parent::display( $tmpl );
    }
}
