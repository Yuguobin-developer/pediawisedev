<?php

// Front end related functionality

class MM_WPFS_Members_Front {

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
		// here is where we check pages for correct role and block if not allowed
		add_action( 'template_redirect', array( $this, 'template_redirect' ) );
	}

	function template_redirect() {
		global $post;
		if ( ! empty( $post ) ) {
			$protect = get_post_meta( $post->ID, 'wpfs_members_post_protect', true );
			// Only check role if protection is enabled and the current user isn't someone who can edit posts
			if ( ! empty( $protect ) && $protect == 1 && ! current_user_can( 'edit_posts' ) ) {
				$allowed_role = get_post_meta( $post->ID, 'wpfs_members_post_role', true );
				$current_user = wp_get_current_user(); //template_redirect comes after init action so this is OK
				$user_roles   = $current_user->roles; //array of string of role names
				$role_ranks   = MM_WPFS_Members::getInstance()->get_role_ranks();

				$allowed = false;
				foreach ( $user_roles as $ur ) {
					// Look for a user role that is ranked higher or equal to the current post allowed role
					if ( isset( $role_ranks[ $ur ] ) && ( $role_ranks[ $ur ] >= $role_ranks[ $allowed_role ] ) ) {
						$allowed = true;
						break;
					}
				}

				if ( ! $allowed ) {
					$this->return_404();
				}
			}
		}
	}

	private function return_404() {
		status_header( 404 );
		nocache_headers();
		include_once get_404_template();
		exit;
	}

	public function add_meta_boxes( $post_type ) {
		do_action( 'wpfs_members_before_add_meta_box', $post_type );

		$post_types = array( 'post', 'page' ); //limit meta box to certain post types
		if ( in_array( $post_type, $post_types ) ) {
			add_meta_box(
				'wpfs_members_custom_user_roles'
				, __( 'WPFS Members', 'wp-full-stripe-members' )
				, array( $this, 'render_meta_box_content' )
				, $post_type
				, 'side'
				, 'high'
			);
		}

		do_action( 'wpfs_members_after_add_meta_box', $post_type );
	}

	public function render_meta_box_content( $post ) {
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'wpfs_members_inner_custom_box', 'wpfs_members_inner_custom_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$wpfs_members_post_role = get_post_meta( $post->ID, 'wpfs_members_post_role', true );
		$protect                = get_post_meta( $post->ID, 'wpfs_members_post_protect', true );

		// Display the form, using the current value.
		$html = '<label for="wpfs_members_new_field">';
		$html .= 'Subscriber Only:&nbsp;&nbsp;';
		$html .= '</label>';
		if ( $protect == 1 ) {
			$html .= '<input type="checkbox" name="wpfs_members_post_protect" id="wpfs_members_post_protect" value="1" checked="checked"><br/><br/>';
		} else {
			$html .= '<input type="checkbox" name="wpfs_members_post_protect" id="wpfs_members_post_protect" value="1" ><br/><br/>';
		}

		$html .= '<label for="wpfs_members_new_field">';
		$html .= 'Select Role:&nbsp;&nbsp;';
		$html .= '</label>';
		$html .= '<select name="wpfs_members_post_role" id="wpfs_members_post_role">';

		$p = '';
		$r = '';

		$available_roles = MM_WPFS_Members::getInstance()->get_wp_roles();
		$role_names      = MM_WPFS_Members::getInstance()->get_wp_role_names();

		foreach ( $available_roles as $role => $details ) {
			// skip the no access role
			if ( $role == 'wpfs_no_access' ) {
				continue;
			}

			$name = translate_user_role( $role_names[ $details->name ] );
			if ( $wpfs_members_post_role == $role ) { // preselect specified role
				$p = "\n\t<option selected='selected' value='" . esc_attr( $role ) . "'>$name</option>";
			} else {
				$r .= "\n\t<option value='" . esc_attr( $role ) . "'>$name</option>";
			}
		}

		$html .= $p . $r;
		$html .= '</select>';
		$html .= '<p class="howto">To make subscriber only, check the box and select the lowest role allowed to view this post. ';
		$html .= '<a href="' . admin_url( 'admin.php?page=fullstripe-members&tab=help' ) . '">Need help?</a></p>';
		$html = apply_filters( 'wpfs_members_meta_box_content', $html, $post, $wpfs_members_post_role );

		echo $html;
	}

	public function save_post( $post_id ) {
		// We need to verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times.

		// Check if our nonce is set.
		if ( ! isset( $_POST['wpfs_members_inner_custom_box_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['wpfs_members_inner_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'wpfs_members_inner_custom_box' ) ) {
			return $post_id;
		}

		// If this is an autosave, our form has not been submitted,  so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		// OK, it is safe for us to save the data now.

		// Sanitize the user input.
		$role    = sanitize_text_field( $_POST['wpfs_members_post_role'] );
		$protect = isset( $_POST['wpfs_members_post_protect'] ) ? 1 : 0;

		do_action( 'wpfs_members_before_save_allowed_role', $post_id, $role );

		// Update the meta fields
		update_post_meta( $post_id, 'wpfs_members_post_protect', $protect );
		update_post_meta( $post_id, 'wpfs_members_post_role', $role );

		do_action( 'wpfs_members_after_save_allowed_role', $post_id, $role );

		return $post_id;
	}
}