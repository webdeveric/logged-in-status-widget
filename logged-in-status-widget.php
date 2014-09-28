<?php
/*
Plugin Name: Logged In Status Widget
Plugin Group: Widgets
Version: 0.2
Description: Show different content if the user is logged in.
Author: Eric King
Author URI: http://webdeveric.com/
*/

class LoggedInStatusWidget extends WP_Widget
{
    function __construct( $name = 'Logged In Status Widget' )
    {
        parent::__construct( false, $name );
    }

    public static function init()
    {
        register_widget( __CLASS__ );
    }

    function widget( $args, $instance )
    {
        extract( $args );
        $title = $body = '';
        if ( is_user_logged_in() ) {
            $title = apply_filters( 'widget_title', $instance['logged_in_title'] );

            global $userdata;
            get_currentuserinfo();

            $title = str_ireplace( '{USERNAME}', $userdata->user_login, $title );
            $body  = str_ireplace( '{USERNAME}', $userdata->user_login, $instance['logged_in_body'] );

        } else {
            $title = apply_filters( 'widget_title', $instance['logged_out_title'] );
            $body  = $instance['logged_out_body'];
        }

        echo $before_widget;

        if ( $title )
            echo $before_title, $title, $after_title;

        echo do_shortcode( $body ), $after_widget;
    }

    function update( $new_instance, $old_instance )
    {
        $instance = $old_instance;
        $instance['logged_out_title'] = wp_kses_data( $new_instance['logged_out_title'] );
        $instance['logged_out_body']  = wp_kses_post( $new_instance['logged_out_body'] );
        $instance['logged_in_title']  = wp_kses_data( $new_instance['logged_in_title'] );
        $instance['logged_in_body']   = wp_kses_post( $new_instance['logged_in_body'] );
        return $instance;
    }

    function form( $instance )
    {
        $logged_out_title = isset( $instance['logged_out_title'] ) ? esc_attr( $instance['logged_out_title'] ) : '';
        $logged_in_title  = isset( $instance['logged_in_title'] ) ? esc_attr( $instance['logged_in_title'] ) : '';
        $logged_out_body  = isset( $instance['logged_out_body'] ) ? esc_html( $instance['logged_out_body'] ) : '';
        $logged_in_body   = isset( $instance['logged_in_body'] ) ? esc_html( $instance['logged_in_body'] ) : '';
        ?>

        <h3>Logged Out Messages</h3>
        <p>
            <label for="<?php echo $this->get_field_id('logged_out_title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('logged_out_title'); ?>" name="<?php echo $this->get_field_name('logged_out_title'); ?>" type="text" value="<?php echo $logged_out_title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('logged_out_body'); ?>"><?php _e('Body:'); ?></label> 
            <textarea rows="8" cols="20" class="widefat" id="<?php echo $this->get_field_id('logged_out_body'); ?>" name="<?php echo $this->get_field_name('logged_out_body'); ?>"><?php echo $logged_out_body; ?></textarea>
        </p>

        <h3>Logged In Messages</h3>
        <p>
            <label for="<?php echo $this->get_field_id('logged_in_title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('logged_in_title'); ?>" name="<?php echo $this->get_field_name('logged_in_title'); ?>" type="text" value="<?php echo $logged_in_title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('logged_in_body'); ?>"><?php _e('Body:'); ?></label> 
            <textarea rows="8" cols="20" class="widefat" id="<?php echo $this->get_field_id('logged_in_body'); ?>" name="<?php echo $this->get_field_name('logged_in_body'); ?>"><?php echo $logged_in_body; ?></textarea>
        </p>

        <?php
    }
}

add_action('widgets_init', array( 'LoggedInStatusWidget', 'init' ) );
