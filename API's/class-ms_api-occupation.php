<?php
/**
 * Created by PhpStorm.
 * User: Mustafa Shaaban
 * Date: 6/20/2019
 * Time: 6:10 PM
 */

class Ms_api_occupation extends WP_REST_Request
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'MS_API_get_occupation'));
    }

    public function MS_API_get_occupation($request)
    {
        /**
         * Handle Forms request.
         */
        register_rest_route('MSAPI', 'forms/occupation', array(
            'methods' => 'POST',
            'callback' => array($this, 'MS_API_forms_endpoint_handler'),
        ));
    }

    public function MS_API_forms_endpoint_handler($request = null)
    {
        $occupation = new WP_Query([
            'post_type' => 'peepso_user_field',
            'title' => 'Occupation',
            'post_status' => 'publish',
            'posts_per_page' => 1
        ]);
        if ($occupation->have_posts()) {
            $occupation_values = get_post_meta($occupation->post->ID, 'select_options', true);
            $response = array(
                'code' => 200,
                'data' => unserialize($occupation_values)
            );
        } else {
            $response = array(
                'code' => 200,
                'message' => 'There are no values available',
                'data' => []
            );
        }

        return new WP_REST_Response($response, 123);

    }


}

new Ms_api_occupation();