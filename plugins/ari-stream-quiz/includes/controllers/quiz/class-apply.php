<?php
namespace Ari_Stream_Quiz\Controllers\Quiz;

class Apply extends Save {
    protected function saved_successfully( $entity ) {
        $url_params = array(
            'page' => 'ari-stream-quiz-quiz',

            'action' => 'edit',

            'id' => $entity->quiz_id,
        );

        parent::saved_successfully( $entity, $url_params );
    }
}
