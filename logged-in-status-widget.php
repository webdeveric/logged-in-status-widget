<?php
/*
Plugin Name: Logged In Status Widget
Plugin Group: Widgets
Version: 0.2
Description: Show different content if the user is logged in.
Author: Eric King
Author URI: http://webdeveric.com/
*/

add_shortcode('login-url', function() {
    return wp_login_url( get_permalink() );
});

add_shortcode('logout-url', function() {
    return wp_logout_url( get_permalink() );
});

class UserInfoShortcode
{
    protected $user;

    public function __construct( WP_User $user )
    {
        $this->user = $user;
        $this->setup_shortcodes();
    }

    protected function setup_shortcodes()
    {
        $fields = array(
            'id',
            'user_login',
            'user_nicename',
            'user_email',
            'user_url',
            'user_registered',
            'user_status',
            'display_name'
        );

        foreach ( $fields as &$field ) {
            add_shortcode('user_' . $field, array( &$this, 'shortcode') );
        }
    }

    public function shortcode( $atts, $content, $shortcode_name )
    {
        $key = substr($shortcode_name, 5);

        if ( $key === 'id' )
            $key = 'ID';

        if ( isset( $this->user->$key ) ) {
            return $this->user->$key;
        }

        return '';
    }
}

class LoggedInStatusWidget extends WP_Widget
{
    public function __construct( $name = 'Logged In Status Widget' )
    {
        parent::__construct( false, $name );
    }

    public function widget( $args, $instance )
    {
        extract( $args );

        if ( is_user_logged_in() ) {

            $title = $instance['logged_in_title'];
            $body  = $instance['logged_in_body'];

        } else {

            $title = $instance['logged_out_title'];
            $body  = $instance['logged_out_body'];

        }

        echo $before_widget;

        if ( ! empty( $title ) )
            echo $before_title, do_shortcode( apply_filters( 'widget_title', $title, $instance, $this->id_base ) ), $after_title;

        echo wpautop( do_shortcode( $body ) ), $after_widget;
    }

    public function update( $new_instance, $old_instance )
    {
        $instance = $old_instance;
        $instance['logged_out_title'] = wp_kses_data( $new_instance['logged_out_title'] );
        $instance['logged_out_body']  = wp_kses_post( $new_instance['logged_out_body'] );
        $instance['logged_in_title']  = wp_kses_data( $new_instance['logged_in_title'] );
        $instance['logged_in_body']   = wp_kses_post( $new_instance['logged_in_body'] );
        return $instance;
    }

    public function form( $instance )
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

add_action( 'widgets_init', function() {
    new UserInfoShortcode( wp_get_current_user() );
    register_widget('LoggedInStatusWidget');
} );
