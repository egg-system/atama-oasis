<?php
/**
 * Custom Text widget class
 *
 * @since 1.0.0
 */
class Bavotasan_Custom_Text_Widget extends WP_Widget {
	function __construct() {
		$widget_ops = array( 'classname' => 'bavotasan_custom_text_widget', 'description' => __( 'Custom Text Widget with Icon', 'arcade-basic' ) );
		parent::__construct( 'bavotasan_custom_text_widget', '(' . BAVOTASAN_THEME_NAME . ') ' . __( 'Icon & Text', 'arcade-basic' ), $widget_ops );

		add_action( 'sidebar_admin_setup', array( $this, 'admin_setup' ) );
	}

	function admin_setup() {
		wp_enqueue_script( 'bavotasan_image_widget', BAVOTASAN_THEME_URL . '/library/js/admin/image-widget.js', array( 'jquery' ), '', true );

		wp_enqueue_style( 'bavotasan_image_widget_css', BAVOTASAN_THEME_URL . '/library/css/admin/image-widget.css' );
		wp_enqueue_style( 'font-awesome', BAVOTASAN_THEME_URL .'/library/css/font-awesome.css', false, '4.7.0', 'all' );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$icon =  ( empty( $instance['icon'] ) ) ? '' : '<i class="' . esc_attr( $instance['button_color'] ) . ' fa ' . strip_tags( $instance['icon'] ). '"></i>';
		$url = ( empty( $instance['url'] ) ) ? '' : esc_url( $instance['url'] );
		$text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
		$button_text = ( empty( $instance['button_text'] ) ) ? '' : $instance['button_text'];

		$icon_string = ( $url ) ? '<a href="' . $url . '">'. $icon . '</a>' : $icon;
		$title_string = ( $url ) ? '<a href="' . $url . '">'. $title . '</a>' : $title;

		// WPML Compatibility
		if ( function_exists( 'icl_register_string' ) ) {
 			icl_register_string( 'Widgets', 'arcade_widget_text_' . $this->number, $text );
 			icl_register_string( 'Widgets', 'arcade_widget_button_text_' . $this->number, $button_text );
 		}

		if ( function_exists( 'icl_t' ) ) {
 			$text = icl_t( 'Widgets', 'arcade_widget_text_' . $this->number, $text );
 			$button_text = icl_t( 'Widgets', 'arcade_widget_button_text_' . $this->number, $button_text );
 		}

		echo $before_widget;

		if ( $icon )
			echo $icon_string;

		if ( $title )
			echo $before_title . $title_string . $after_title;
		?>

		<div class="textwidget">
			<?php echo ( ! empty( $instance['filter'] ) ) ? wpautop( $text ) : $text; ?>
		</div>
		<?php
		if ( $url && $button_text )
			echo '<a href="' . $url . '" class="btn btn-' . esc_attr( $instance['button_color'] ) . ' btn-lg">' . $button_text . '</a>';

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['icon'] = strip_tags( $new_instance['icon'] );
		$instance['url'] = esc_url( $new_instance['url'] );
		$instance['button_color'] = esc_attr( $new_instance['button_color'] );

		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['text'] =  $new_instance['text'];
			$instance['button_text'] =  $new_instance['button_text'];
		} else {
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['text'] ) ) ); // wp_filter_post_kses() expects slashed
			$instance['button_text'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['button_text'] ) ) );
		}

		$instance['filter'] = isset( $new_instance['filter'] );

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'icon' => '', 'url' => '', 'button_text' => '', 'button_color' => 'info' ) );
		extract( $instance );
		$icon_tag = ( $icon ) ? '<i class="fa ' . esc_attr( $icon ) . '"></i>' : '';
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'arcade-basic' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><label><?php _e( 'Icon:', 'arcade-basic' ); ?></label>
		<span class="custom-icon-container"><?php echo $icon_tag; ?></span>
		<a href="#" class="view-icons"><?php _e( 'View Icons', 'arcade-basic' ); ?></a> | <a href="#" class="delete-icon"><?php _e( 'Remove Icon', 'arcade-basic' ); ?></a>
		<?php bavotasan_font_awesome_icons(); ?>
		<input class="image-widget-custom-icon" name="<?php echo $this->get_field_name( 'icon' ); ?>" type="hidden" value="<?php echo esc_attr( $icon ); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e( 'URL:', 'arcade-basic' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" type="text" value="<?php echo esc_attr( $url ); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'button_text' ); ?>"><?php _e( 'Button Text:', 'arcade-basic' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'button_text' ); ?>" name="<?php echo $this->get_field_name( 'button_text' ); ?>" type="text" value="<?php echo esc_attr( $button_text ); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'button_color' ); ?>"><?php _e( 'Button & Icon Color:', 'arcade-basic' ); ?></label>
		<select name="<?php echo $this->get_field_name( 'button_color' ); ?>" id="<?php echo $this->get_field_id( 'button_color' ); ?>" class="widefat">
		<?php
		$options = array(
			'default' => __( 'Default', 'arcade-basic' ),
			'info' => __( 'Light Blue', 'arcade-basic' ),
			'primary' => __( 'Blue', 'arcade-basic' ),
			'danger' => __( 'Red', 'arcade-basic' ),
			'warning' => __( 'Yellow', 'arcade-basic' ),
			'success' => __( 'Green', 'arcade-basic' ),
		);
		foreach ( $options as $value => $key ) {
			echo '<option value="' . $value . '" ' . selected( $button_color, $value, false ) . '>' . $key . '</option>';
		}
		?>
		</select></p>

		<p><label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Text:', 'arcade-basic' ); ?></label>
		<textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo esc_textarea( $text ); ?></textarea>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked( isset( $filter ) ? $filter : 0 ); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e( 'Automatically add paragraphs', 'arcade-basic' ); ?></label></p>
		<?php
	}
}
register_widget( 'Bavotasan_Custom_Text_Widget' );