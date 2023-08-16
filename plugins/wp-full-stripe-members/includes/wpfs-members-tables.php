<?php

class WPFS_Members_Legacy_Table extends WP_List_Table {
	function __construct() {
		parent::__construct( array(
			'singular' => 'Member', //Singular label
			'plural'   => 'Members', //plural label, also this well be one of the table css class
			'ajax'     => false //We won't support Ajax for this table
		) );
	}

	/**
	 * Add extra markup in the toolbars before or after the list
	 *
	 * @param string $which , helps you decide if you add the markup after (bottom) or before (top) the list
	 */
	function extra_tablenav( $which ) {
		if ( $which == "top" ) {
			//The code that goes before the table is here
			echo '<div class="wrap">';
		}
		if ( $which == "bottom" ) {
			//The code that goes after the table is there
			echo '</div>';
		}
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	function prepare_items() {
		global $wpdb;
		$screen = get_current_screen();

		// Preparing your query
		$query = "SELECT * FROM " . $wpdb->prefix . 'fullstripe_members';

		//Parameters that are going to be used to order the result
		$orderby = ! empty( $_REQUEST["orderby"] ) ? esc_sql( $_REQUEST["orderby"] ) : 'ASC';
		$order   = ! empty( $_REQUEST["order"] ) ? esc_sql( $_REQUEST["order"] ) : '';
		if ( ! empty( $orderby ) && ! empty( $order ) ) {
			$query .= ' ORDER BY ' . $orderby . ' ' . $order;
		}

		//Number of elements in your table?
		$totalitems = $wpdb->query( $query ); //return the total number of affected rows
		//How many to display per page?
		$perpage = 10;
		//Which page is this?
		$paged = ! empty( $_GET["paged"] ) ? esc_sql( $_GET["paged"] ) : '';
		//Page Number
		if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1;
		}
		//How many pages do we have in total?
		$totalpages = ceil( $totalitems / $perpage );
		//adjust the query to take pagination into account
		if ( ! empty( $paged ) && ! empty( $perpage ) ) {
			$offset = ( $paged - 1 ) * $perpage;
			$query .= ' LIMIT ' . (int) $offset . ',' . (int) $perpage;
		}

		// Register the pagination
		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page"    => $perpage,
		) );
		//The pagination links are automatically built according to those parameters

		//Register the Columns
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// Fetch the items
		$this->items = $wpdb->get_results( $query );
	}

	/**
	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
	 */
	function get_columns() {
		return $columns = array(
			'memberID'         => __( 'Member ID', 'wp-full-stripe-members' ),
			'email'            => __( 'Email', 'wp-full-stripe-members' ),
			'role'             => __( 'Role', 'wp-full-stripe-members' ),
			'plan'             => __( 'Plan ID', 'wp-full-stripe-members' ),
			'stripeCustomerID' => __( 'Stripe Customer ID', 'wp-full-stripe-members' ),
			'created'          => __( 'Joined', 'wp-full-stripe-members' ),
			'actions'          => __( 'Actions', 'wp-full-stripe-members' )
		);
	}

	/**
	 * Decide which columns to activate the sorting functionality on
	 * @return array $sortable, the array of columns that can be sorted by the user
	 */
	public function get_sortable_columns() {
		return $sortable = array(
			'role'    => array( 'role', false ),
			'plan'    => array( 'plan', false ),
			'created' => array( 'created', false )
		);
	}

	/**
	 * Display the rows of records in the table
	 * @return string, echo the markup of the rows
	 */
	function display_rows() {
		global $wp_roles;
		$role_names = $wp_roles->get_names();
		//Get the records registered in the prepare_items method
		$records = $this->items;

		//Get the columns registered in the get_columns and get_sortable_columns methods
		list( $columns, $hidden ) = $this->get_column_info();

		//Loop for each record
		if ( ! empty( $records ) ) {
			foreach ( $records as $rec ) {
				//Open the line
				echo '<tr id="record_' . $rec->memberID . '">';
				foreach ( $columns as $column_name => $column_display_name ) {
					//Style attributes for each col
					$class = "class='$column_name column-$column_name'";
					$style = "";
					if ( in_array( $column_name, $hidden ) ) {
						$style = ' style="display:none;"';
					}
					$attributes = $class . $style;

					//Display the cell
					switch ( $column_name ) {
						case "memberID":
							echo '<td ' . $attributes . '>' . stripslashes( $rec->memberID ) . '</td>';
							break;
						case "email":
							echo '<td ' . $attributes . '>' . $rec->email . '</td>';
							break;
						case "role":
							echo '<td ' . $attributes . '>' . $role_names[ $rec->role ] . '</td>';
							break;
						case "plan":
							echo '<td ' . $attributes . '>' . $rec->plan . '</td>';
							break;
						case "stripeCustomerID":
							$stripeLink = "<a href='https://dashboard.stripe.com/";
							if ( $rec->livemode == 0 ) {
								$stripeLink .= 'test/';
							}
							$stripeLink .= "customers/" . $rec->stripeCustomerID . "'>$rec->stripeCustomerID</a>";
							echo '<td ' . $attributes . '>' . $stripeLink . '</td>';
							break;
						case "created":
							echo '<td ' . $attributes . '>' . date( 'F jS Y H:i', strtotime( $rec->created ) ) . '</td>';
							break;
						case "actions":
							$editActionLink = add_query_arg( array(
								'page'   => 'wpfs-members-edit',
								'member' => $rec->memberID
							), admin_url( 'admin.php' ) );
							$actions        = "<td $attributes>";
							$actions .= "<a class=\"button\" href=\"$editActionLink\">" . __( 'Edit', 'wp-full-stripe-members' ) . '</a>';
							$actions .= '<span class="form-action-last">';
							$actions .= "<button class=\"button delete\" data-id=\"$rec->memberID\" data-type=\"member\">" . __( 'Delete', 'wp-full-stripe-members' ) . '</button>';
							$actions .= '</span>';
							$actions .= '</td>';
							echo $actions;
							break;
					}
				}

				//Close the line
				echo '</tr>';
			}
		}
	}
}

class WPFS_Members_Table extends WPFS_Base_Table {

	public function __construct() {
		parent::__construct( array(
			'singular' => __( 'Member', 'wp-full-stripe-members' ),
			'plural'   => __( 'Members', 'wp-full-stripe-members' ),
			'ajax'     => false
		) );
	}

	public function no_items() {
		_e( 'No members found.', 'wp-full-stripe-members' );
	}

	public function prepare_items() {
		global $wpdb;

		$query = "SELECT m.memberID,m.role,m.plan,m.email,m.wpUserID,m.stripeCustomerID,m.stripeSubscriptionID,m.stripeSubscriptionStatus,m.livemode,m.created,u.user_login,u.display_name FROM {$wpdb->prefix}fullstripe_members m LEFT OUTER JOIN {$wpdb->base_prefix}users u ON m.wpUserID = u.ID";

		$where_statement = null;

		$member = ! empty( $_REQUEST["member"] ) ? esc_sql( trim( $_REQUEST["member"] ) ) : null;
		$status = ! empty( $_REQUEST["status"] ) ? esc_sql( trim( $_REQUEST["status"] ) ) : null;
		$mode   = ! empty( $_REQUEST["mode"] ) ? esc_sql( trim( $_REQUEST["mode"] ) ) : null;

		if ( isset( $member ) ) {
			if ( ! isset( $where_statement ) ) {
				$where_statement = ' WHERE ';
			} else {
				$where_statement .= ' AND ';
			}
			$where_statement .= sprintf( "(LOWER(m.memberID) LIKE LOWER('%s') OR LOWER(m.email) LIKE LOWER('%s') OR m.stripeCustomerID LIKE '%s' OR LOWER(u.user_login) LIKE LOWER('%s') OR LOWER(u.display_name) LIKE LOWER('%s'))", "%$member%", "%$member%", "%$member%", "%$member%", "%$member%" );
		}

		if ( isset( $status ) ) {
			if ( ! isset( $where_statement ) ) {
				$where_statement = ' WHERE ';
			} else {
				$where_statement .= ' AND ';
			}
			$where_statement .= sprintf( "(m.stripeSubscriptionStatus LIKE '%s')", "%$status%" );
		}

		if ( isset( $mode ) ) {
			if ( ! isset( $where_statement ) ) {
				$where_statement = ' WHERE ';
			} else {
				$where_statement .= ' AND ';
			}
			$where_statement .= sprintf( '(m.livemode = %d)', $mode == 'live' ? 1 : 0 );
		}

		if ( isset( $where_statement ) ) {
			$query .= $where_statement;
		}

		$order_by = ! empty( $_REQUEST["orderby"] ) ? esc_sql( $_REQUEST["orderby"] ) : 'created';
		$order    = ! empty( $_REQUEST["order"] ) ? esc_sql( $_REQUEST["order"] ) : ( empty( $_REQUEST['orderby'] ) ? 'DESC' : 'ASC' );
		if ( ! empty( $order_by ) && ! empty( $order ) ) {
			$query .= " ORDER BY $order_by $order";
		}

		$total_items = $wpdb->query( $query );
		$per_page    = 10;
		$total_pages = ceil( $total_items / $per_page );
		$this->set_pagination_args( array(
			"total_items" => $total_items,
			"total_pages" => $total_pages,
			"per_page"    => $per_page,
		) );
		$current_page = $this->get_pagenum();
		if ( ! empty( $current_page ) && ! empty( $per_page ) ) {
			$offset = ( $current_page - 1 ) * $per_page;
			$query .= ' LIMIT ' . (int) $offset . ',' . (int) $per_page;
		}

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->items = $wpdb->get_results( $query );
	}

	public function get_columns() {
		return array(
			'action'        => __( 'Actions', 'wp-full-stripe-members' ),
			'id'            => __( 'ID', 'wp-full-stripe-members' ),
			'member'        => $this->format_column_header_title( __( 'Member', 'wp-full-stripe-members' ), array(
				__( 'User', 'wp-full-stripe-members' ),
				__( 'Role', 'wp-full-stripe-members' )
			) ),
			'member_plan'   => $this->format_column_header_title( __( 'Stripe subscription', 'wp-full-stripe-members' ), array(
				__( 'Subscriber', 'wp-full-stripe-members' ),
				__( 'Plan', 'wp-full-stripe-members' )
			) ),
			'member_status' => $this->format_column_header_title( __( 'Status', 'wp-full-stripe-members' ), array(
				__( 'Status', 'wp-full-stripe-members' ),
				__( 'Mode', 'wp-full-stripe-members' )
			) ),
			'created'       => __( 'Created at', 'wp-full-stripe-members' )
		);
	}

	protected function get_sortable_columns() {
		return array(
			'created' => array( 'created', false )
		);
	}

	public function display_rows() {

		$items = $this->items;

		if ( ! empty( $items ) ) {

			global $wp_roles;
			$role_names = $wp_roles->get_names();
			list( $columns, $hidden ) = $this->get_column_info();

			foreach ( $items as $item ) {
				$row = '';
				$row .= "<tr id=\"record_{$item->memberID}\">";
				foreach ( $columns as $column_name => $column_display_name ) {
					$class = "class=\"$column_name column-$column_name\"";
					$style = "";
					if ( in_array( $column_name, $hidden ) ) {
						$style = "style=\"display:none;\"";
					}
					$attributes = "{$class} {$style}";

					switch ( $column_name ) {
						case "id":
							$row .= "<td {$attributes}><b>{$item->memberID}</b></td>";
							break;
						case "member":
							$user = $item->display_name;
							if ( ! empty( $user ) ) {
								$user_label = stripslashes( $user );
							} else {
								$user_label = $item->user_login;
							}
							$edit_user_href = add_query_arg(
								array(
									'user_id'          => $item->wpUserID,
									'_wp_http_referer' => esc_attr( wp_unslash( $_SERVER['REQUEST_URI'] ) )
								),
								admin_url( 'user-edit.php' )
							);
							$row .= "<td {$attributes}><a href=\"{$edit_user_href}\" title=\"{$item->email}\">{$user_label}</a><br/>{$role_names[ $item->role ]}</td>";
							break;
						case "member_plan":
							$stripe_customer_href   = $this->build_stripe_customer_link( $item->stripeCustomerID, $item->livemode );
							$stripeSubscriptionLink = "<a href=\"{$stripe_customer_href}\" target=\"_blank\">{$item->email}</a>";
							$row .= "<td {$attributes}>{$stripeSubscriptionLink}<br/>{$item->plan}</td>";
							break;
						case "member_status":
							$status_Label    = ucfirst( $item->stripeSubscriptionStatus );
							$live_mode_label = $item->livemode == 0 ? __( 'Test', 'wp-full-stripe-members' ) : __( 'Live', 'wp-full-stripe-members' );
							$row .= "<td {$attributes}><b>{$status_Label}</b><br/>$live_mode_label</td>";
							break;
						case "created":
							$row .= "<td {$attributes}>" . date( 'F jS Y H:i', strtotime( $item->created ) ) . "</td>";
							break;
						case "action":
							$edit_action_link = add_query_arg( array(
								'page'   => 'wpfs-members-edit',
								'member' => $item->memberID
							), admin_url( 'admin.php' ) );
							$edit_title       = __( 'Edit', 'wp-full-stripe-members' );
							$delete_title     = __( 'Delete', 'wp-full-stripe-members' );
							$row .= "<td {$attributes}>";
							$row .= "<a class=\"button button-primary\" href=\"$edit_action_link\" title=\"{$edit_title}\"><i class=\"fa fa-pencil fa-fw\"></i></a>";
							$row .= "<span class=\"form-action-last\">";
							$row .= "<button class=\"button delete\" data-id=\"$item->memberID\" data-type=\"member\" title=\"{$delete_title}\"><i class=\"fa fa-trash-o fa-fw\"></i></button>";
							$row .= '</span>';
							$row .= '</td>';
							break;
					}

				}

				$row .= "</tr>";

				echo $row;
			}
		}
	}

	protected function get_table_classes() {
		$table_classes = parent::get_table_classes();

		return array_diff( $table_classes, array( 'fixed' ) );
	}

}
