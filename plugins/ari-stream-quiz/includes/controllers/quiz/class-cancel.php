<?php
namespace Ari_Stream_Quiz\Controllers\Quiz;

use Ari\Controllers\Controller as Controller;
use Ari\Utils\Response as Response;
use Ari_Stream_Quiz\Helpers\Helper as Helper;

class Cancel extends Controller {
    public function execute() {
        Response::redirect(
            Helper::build_url(
                array(
                    'page' => 'ari-stream-quiz-quizzes',
                ),
                array(
                    'id',
                    'type',
                )
            )
        );
    }
}