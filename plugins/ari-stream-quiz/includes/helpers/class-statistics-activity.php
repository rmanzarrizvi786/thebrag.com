<?php
namespace Ari_Stream_Quiz\Helpers;

use Ari_Stream_Quiz\Helpers\Quiz_Activity as Quiz_Activity;
use Ari\Utils\Enum as Enum;

class Statistics_Activity extends Enum {
    const VIEW = 'VIEW';

    const START = 'START';

    const COMPLETE = 'COMPLETE';

    const SHARE = 'SHARE';

    const OPT_IN = 'OPT_IN';

    static public function convert_quiz_activity( $val ) {
        if ( Quiz_Activity::FORCE_FACEBOOK === $val || strpos( $val, 'SHARE_' ) === 0 )
            return self::SHARE;

        if ( Quiz_Activity::OPT_IN === $val )
            return self::OPT_IN;

        return false;
    }
}
