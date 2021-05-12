<?php
namespace Ari_Stream_Quiz\Entities;

use Ari\Wordpress\Helper as WP_Helper;
use Ari\Wordpress\Mail as Mail;
use Ari\Entities\Entity as Entity;
use Ari_Stream_Quiz\Entities\Statistics as Quiz_Stat;
use Ari\Template\Template as Template;
use Ari_Stream_Quiz\Helpers\Settings as Settings;

class Quiz extends Entity {
    public $quiz_id;

    public $quiz_title = '';

    public $quiz_title_filtered = '';

    public $quiz_description = '';

    public $quiz_image_id = 0;

    public $quiz_image = array();

    public $quiz_type = '';

    public $shuffle_answers = 0;

    public $random_questions = 0;

    public $random_question_count = 0;

    public $use_paging = 0;

    public $questions_per_page = 0;

    public $start_immediately = 0;

    public $question_count = 0;

    public $theme = '';

    public $collect_data = 0;

    public $collect_email = 1;

    public $collect_name = 0;

    public $collect_data_optional = 0;

    public $author_id = 0;

    public $created = '0000-00-00 00:00:00';

    public $modified = '0000-00-00 00:00:00';

    public $post_id = 0;

    public $quiz_meta = '';

    public $questions = array();

    protected $bool_fields = array(
        'random_questions',

        'shuffle_answers',

        'start_immediately',

        'use_paging',

        'collect_data',

        'collect_data_optional',

        'collect_name',

        'collect_email',
    );

    protected $meta_fields = array(
        'mailchimp' => array(
            'enabled' => false,

            'list_id' => '',

            'list_name' => '',
        ),

        'aweber' => array(
            'enabled' => false,

            'list_id' => '',

            'list_name' => '',
        ),

        'zapier' => array(
            'enabled' => false,

            'webhook_url' => '',
        ),

        'getresponse' => array(
            'enabled' => false,

            'campaign_id' => '',

            'campaign_name' => '',
        ),

        'drip' => array(
            'enabled' => false,

            'campaign_id' => '',

            'campaign_name' => '',
        ),

        'activecampaign' => array(
            'enabled' => false,

            'list_id' => '',

            'list_name' => '',
        ),

        'mailerlite' => array(
            'enabled' => false,

            'list_id' => '',

            'list_name' => '',
        ),

        'constantcontact' => array(
            'enabled' => false,

            'list_id' => '',

            'list_name' => '',
        ),

        'share_to_see' => false,

        'show_share_buttons' => false,

        'paging_nav_button' => false,

        'show_results' => '',

        'try_again_button' => false,

        'shortcode' => false,

        'show_quiz_image' => false,

        'content' => array(
            'enabled' => false,

            'before_result' => '',

            'after_result' => '',

            'before_lead_form' => '',

            'after_lead_form' => '',
        ),

        'send_mail' => array(
            'enabled' => false,

            'subject' => '',

            'template' => '',
        ),

        'lead_form' => array(
            'ask_confirmation' => false,

            'ask_confirmation_enabled' => false,

            'confirmation_label' => '',
        ),
    );

    protected $json_fields = array();

    protected $image_size = null;

    function __construct( $db ) {
        parent::__construct( 'asq_quizzes', 'quiz_id', $db );

        $this->quiz_meta = json_decode( json_encode( $this->meta_fields ) );
    }

    public function bind( $data, $ignore = array() ) {
        if ( ! $this->is_new() ) {
            $ignore[] = 'quiz_type';
        }

        foreach ( $this->json_fields as $json_field ) {
            if ( ! empty( $data[$json_field] ) && is_string( $data[$json_field] ) ) {
                $data[$json_field] = json_decode( $data[$json_field], false );
            }
        }

        return parent::bind( $data, $ignore );
    }

    public function store( $force_insert = false ) {
        $is_new = $this->is_new();
        $current_time_db_gmt = current_time( 'mysql', 1 );

        if ( $is_new ) {
            $this->author_id = get_current_user_id();
            $this->created = $current_time_db_gmt;
        } else {
            $this->modified = $current_time_db_gmt;
        }

        $this->quiz_title_filtered = strip_tags( $this->quiz_title );
        $this->question_count = count( $this->questions );

        $quiz_meta = $this->quiz_meta;
        if ( ! is_string( $this->quiz_meta ) )
            $this->quiz_meta = json_encode( $this->quiz_meta );

        $result = parent::store( $force_insert );

        $this->quiz_meta = $quiz_meta;

        if ( $result ) {
            if ( $is_new ) {
                $quiz_stat = new Quiz_Stat( $this->db );
                $quiz_stat->bind( array( 'quiz_id' => $this->quiz_id ) );
                $quiz_stat->store();
            }

            $update_data = array();
            if ( $this->post_id == 0 ) {
                $post_id = wp_insert_post(
                    array(
                        'post_title' => $this->quiz_id,

                        'post_type' => ARISTREAMQUIZ_POST_TYPE,

                        'post_status' => 'publish',

                        'meta_input' => array(
                            'quiz_id' => $this->quiz_id,

                            'quiz_title' => $this->quiz_title
                        )
                    )
                );

                if ( $post_id ) {
                    $this->post_id = $post_id;

                    $this->db->update(
                        $this->db_tbl,
                        array(
                            'post_id' => $post_id,
                        ),
                        array(
                            'quiz_id' => $this->quiz_id,
                        )
                    );
                    $update_data['post_id'] = $post_id;

                    global $wp_version;

                    if ( $wp_version && version_compare( $wp_version, '4.4.0', '<' ) ) {
                        add_post_meta( $post_id, 'quiz_id', $this->quiz_id, true );
                        add_post_meta( $post_id, 'quiz_title', $this->quiz_title, true );
                    }
                }
            } else {
                update_post_meta( $this->post_id, 'quiz_title', $this->quiz_title );
            }
        }

        return $result;
    }

    public function copy() {
        if ( $this->is_new() )
            return false;

        $quiz_class = get_class( $this );

        $quiz_copy = new $quiz_class( $this->db );

        if ( ! $quiz_copy->bind(
                $this->to_array(
                    array(
                        'quiz_id',

                        'post_id',

                        'author_id',

                        'created',

                        'modified',
                    )
                )
            )
        ) {
            return false;
        }

        $quiz_copy->quiz_title = $this->get_unique_name();

        $this->prepare_copy( $quiz_copy );
        if ( ! $quiz_copy->store() )
            return false;

        return $quiz_copy;
    }

    public function load( $keys, $reset = true ) {
        $result = parent::load( $keys, $reset );

        if ( ! $result )
            return $result;

        if ( $this->quiz_meta ) {
            $quiz_meta = $this->meta_fields;
            $db_quiz_meta = json_decode( $this->quiz_meta, true );

            if ( is_array( $db_quiz_meta ) ) {
                $quiz_meta = array_replace_recursive( $quiz_meta, $db_quiz_meta );
            }

            $this->quiz_meta = json_decode( json_encode( $quiz_meta ) );
        } else {
            $this->quiz_meta = new \stdClass();
        }

        $images = $this->get_images();
        if ( $this->quiz_image_id > 0 ) {
            $this->quiz_image = $this->get_image( $this->quiz_image_id, $images );
        }

        $this->populate_entity( $images );

        return $result;
    }

    protected function prepare_copy( $quiz_copy ) {

    }

    public function collect_data() {
        if ( ! $this->collect_data )
            return false;

        if ( ! $this->collect_name && ! $this->collect_email )
            return false;

        return true;
    }

    public function need_to_process_user_data() {
        return
            (bool)$this->quiz_meta->send_mail->enabled ||
            (bool)$this->quiz_meta->mailchimp->enabled ||
            (bool)$this->quiz_meta->aweber->enabled  ||
            (bool)$this->quiz_meta->zapier->enabled ||
            (bool)$this->quiz_meta->getresponse->enabled ||
            (bool)$this->quiz_meta->drip->enabled ||
            (bool)$this->quiz_meta->activecampaign->enabled ||
            (bool)$this->quiz_meta->mailerlite->enabled ||
            (bool)$this->quiz_meta->constantcontact->enabled;
    }

    public function get_unique_name( $name = null, $title_db_field = 'quiz_title' ) {
        return parent::get_unique_name( $name, $title_db_field );
    }

    public function validate() {
        if ( empty( $this->quiz_title ) )
            return false;

        return true;
    }

    public function is_multi_pages() {
        return
            $this->use_paging &&
            $this->questions_per_page > 0 &&
            (
                ( 0 == $this->random_question_count && $this->questions_per_page < $this->question_count)
                ||
                ( $this->random_question_count > 0 && $this->questions_per_page < $this->random_question_count )
            );
    }

    public function get_filtered_title() {
        return $this->quiz_title_filtered ? $this->quiz_title_filtered : strip_tags( $this->quiz_title );
    }

    protected function populate_entity( $images ) {

    }

    protected function get_images() {
        return array();
    }

    protected function get_image( $image_id, $images, $default_image = array() ) {
        if ( 0 == $image_id || ! isset( $images[$image_id] ) )
            return $default_image;

        $img = $images[$image_id];

        $image = new \stdClass();
        $image->url = $img->url;
        $image->description = $img->description;
        $image->width = $img->width;
        $image->height = $img->height;

        return $image;
    }

    public function set_image_size( $image_size ) {
        $this->image_size = $image_size;
    }

    public function send_mail( $email, $quiz_results ) {
        if ( empty( $email ) )
            return false;

        $subject = $this->compose_mail_subject( $this->quiz_meta->send_mail->subject, $quiz_results );
        $body = $this->compose_mail_body( $this->quiz_meta->send_mail->template, $quiz_results );
        $options = array(
            'from' => Settings::get_option( 'mail_from' ),

            'from_name' => Settings::get_option( 'mail_from_name' ),
        );

        return Mail::send( $email, $subject, $body, $options );
    }

    protected function compose_mail_subject( $template, $quiz_results ) {
        $subject = Template::parse( $template, $quiz_results );

        return $subject;
    }

    protected function compose_mail_body( $template, $quiz_results ) {
        $body = Template::parse( $template, $quiz_results );

        return $body;
    }
}
