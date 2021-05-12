<?php
namespace Ari_Stream_Quiz\Controllers\Quizzes;

use Ari\Controllers\Controller as Controller;
use Ari\Utils\Response as Response;
use Ari_Stream_Quiz\Helpers\Helper as Helper;

class Reset extends Controller {
    public function execute() {
        $model = $this->model();

        $model->set_state( 'filter', null );

        Response::redirect(
            Helper::build_url(
                array(
                    'page' => 'ari-stream-quiz-quizzes',
                )
            )
        );
    }
}
