<?php

class WPFS_Base_Table extends WP_List_Table {

	const HTTPS_DASHBOARD_STRIPE_COM = "https://dashboard.stripe.com/";
	const PATH_TEST = "test/";
	const PATH_CUSTOMERS = 'customers/';
	const PATH_CHARGES = 'charges/';
	const PATH_PAYMENTS = 'payments/';
	const PATH_SUBSCRIPTIONS = 'subscriptions/';

	public function print_column_headers( $with_id = true ) {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$current_url = remove_query_arg( 'paged', $current_url );

		if ( isset( $_GET['orderby'] ) ) {
			$current_orderby = $_GET['orderby'];
		} else {
			$current_orderby = '';
		}

		if ( isset( $_GET['order'] ) && 'desc' === $_GET['order'] ) {
			$current_order = 'desc';
		} else {
			$current_order = 'asc';
		}

		if ( ! empty( $columns['cb'] ) ) {
			static $cb_counter = 1;
			$columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All' ) . '</label>'
			                 . '<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
			$cb_counter ++;
		}

		foreach ( $columns as $column_key => $column_display_name ) {
			$class = array( 'manage-column', "column-$column_key" );

			if ( in_array( $column_key, $hidden ) ) {
				$class[] = 'hidden';
			}

			if ( 'cb' === $column_key ) {
				$class[] = 'check-column';
			} elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) ) {
				$class[] = 'num';
			}

			if ( $column_key === $primary ) {
				$class[] = 'column-primary';
			}

			if ( isset( $sortable[ $column_key ] ) ) {
				list( $orderby, $desc_first ) = $sortable[ $column_key ];

				if ( $current_orderby === $orderby ) {
					$order   = 'asc' === $current_order ? 'desc' : 'asc';
					$class[] = 'sorted';
					$class[] = $current_order;
				} else {
					$order   = $desc_first ? 'desc' : 'asc';
					$class[] = 'sortable';
					$class[] = $desc_first ? 'asc' : 'desc';
				}

				if ( strpos( $column_display_name, '<br>' ) ) {
					$column_display_name_parts = explode( '<br>', $column_display_name );
					foreach ( $column_display_name_parts as $i => $part ) {
						if ( $i == 0 ) {
							$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $part . '</span><span class="sorting-indicator"></span></a>';
						} else {
							$column_display_name .= '<span class="wpfs-table-sub-header">' . $part . '</span>';
						}
					}
				} else {
					$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
				}
			}

			$tag   = ( 'cb' === $column_key ) ? 'td' : 'th';
			$scope = ( 'th' === $tag ) ? 'scope="col"' : '';
			$id    = $with_id ? "id='$column_key'" : '';

			if ( ! empty( $class ) ) {
				$class = "class='" . join( ' ', $class ) . "'";
			}

			echo "<$tag $scope $id $class>$column_display_name</$tag>";
		}
	}

	/**
	 * @param $title
	 *
	 * @param $aggregated_columns
	 *
	 * @return string
	 */
	protected function format_column_header_title( $title, array $aggregated_columns = null ) {
		$column_label = "<b>{$title}</b>";
		if ( ! empty( $aggregated_columns ) ) {
			$size = sizeof( $aggregated_columns );
			$column_label .= '<br>';
			foreach ( $aggregated_columns as $key => $value ) {
				$column_label .= $value;
				if ( $key < $size - 1 ) {
					$column_label .= ' / ';
				}
			}
		}

		return $column_label;
	}

	/**
	 * @param $stripe_customer_id
	 * @param $live_mode
	 *
	 * @return string
	 */
	protected function build_stripe_customer_link( $stripe_customer_id, $live_mode ) {
		$href = $this->build_stripe_base_url( $live_mode );
		$href .= self::PATH_CUSTOMERS . $stripe_customer_id;

		return $href;
	}

	/**
	 * @param $live_mode
	 *
	 * @return string
	 */
	protected function build_stripe_base_url( $live_mode ) {
		$href = self::HTTPS_DASHBOARD_STRIPE_COM;
		if ( $live_mode == 0 ) {
			$href .= self::PATH_TEST;
		}

		return $href;
	}

	/**
	 * @param $stripe_subscription_id
	 * @param $live_mode
	 *
	 * @return string
	 */
	protected function build_stripe_subscription_link( $stripe_subscription_id, $live_mode ) {
		$href = $this->build_stripe_base_url( $live_mode );
		$href .= self::PATH_SUBSCRIPTIONS . $stripe_subscription_id;

		return $href;
	}

	/**
	 * @deprecated
	 *
	 * @param $stripe_charge_id
	 * @param $live_mode
	 *
	 * @return string
	 */
	protected function build_stripe_charge_link( $stripe_charge_id, $live_mode ) {
		$href = $this->build_stripe_base_url( $live_mode );
		$href .= self::PATH_CHARGES . $stripe_charge_id;

		return $href;
	}

	/**
	 * @param $stripe_charge_id
	 * @param $live_mode
	 *
	 * @return string
	 */
	protected function build_stripe_payments_link( $stripe_charge_id, $live_mode ) {
		$href = $this->build_stripe_base_url( $live_mode );
		$href .= self::PATH_PAYMENTS . $stripe_charge_id;

		return $href;
	}

	/**
	 * Add extra markup in the toolbars before or after the list
	 *
	 * @param string $which , helps you decide if you add the markup after (bottom) or before (top) the list
	 */
	protected function extra_tablenav( $which ) {
		if ( $which == "top" ) {
			echo '<div class="wrap">';
		}
		if ( $which == "bottom" ) {
			echo '</div>';
		}
	}

}

/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2016.02.18.
 * Time: 9:17
 */
class WPFS_Multiple_Subscribers_Table extends WPFS_Base_Table {

	const MULTIPLICATION_MARK = 'x';

	public function __construct() {
		parent::__construct( array(
			'singular' => __( 'Subscriber', MM_WPFS::L10N_DOMAIN_ADMIN ),
			'plural'   => __( 'Subscribers', MM_WPFS::L10N_DOMAIN_ADMIN ),
			'ajax'     => false
		) );
	}

	public function no_items() {
		_e( 'No subscriptions found.', MM_WPFS::L10N_DOMAIN_ADMIN );
	}

	public function prepare_items() {
		global $wpdb;

		$query = "SELECT subscriberID,stripeCustomerID,stripeSubscriptionID,chargeMaximumCount,chargeCurrentCount,status,name,email,planID,quantity,addressLine1,addressLine2,addressCity,addressState,addressZip,addressCountry,created,cancelled,livemode,formId,formName,vatPercent FROM {$wpdb->prefix}fullstripe_subscribers";

		$where_statement = null;

		$subscriber   = ! empty( $_REQUEST["subscriber"] ) ? esc_sql( trim( $_REQUEST["subscriber"] ) ) : null;
		$subscription = ! empty( $_REQUEST["subscription"] ) ? esc_sql( trim( $_REQUEST["subscription"] ) ) : null;
		$mode         = ! empty( $_REQUEST["mode"] ) ? esc_sql( trim( $_REQUEST["mode"] ) ) : null;

		if ( isset( $subscriber ) ) {
			if ( ! isset( $where_statement ) ) {
				$where_statement = ' WHERE ';
			} else {
				$where_statement .= ' AND ';
			}
			$where_statement .= sprintf( "(LOWER(name) LIKE LOWER('%s') OR LOWER(email) LIKE LOWER('%s') OR stripeCustomerID LIKE '%s')", "%$subscriber%", "%$subscriber%", "%$subscriber%" );
		}

		if ( isset( $subscription ) ) {
			if ( ! isset( $where_statement ) ) {
				$where_statement = ' WHERE ';
			} else {
				$where_statement .= ' AND ';
			}
			$where_statement .= sprintf( "(stripeSubscriptionID LIKE '%s')", "%$subscription%" );
		}

		if ( isset( $mode ) ) {
			if ( ! isset( $where_statement ) ) {
				$where_statement = ' WHERE ';
			} else {
				$where_statement .= ' AND ';
			}
			$where_statement .= sprintf( '(livemode = %d)', $mode == 'live' ? 1 : 0 );
		}

		if ( isset( $where_statement ) ) {
			$query .= $where_statement;
		}

		$order_by = ! empty( $_REQUEST["orderby"] ) ? esc_sql( $_REQUEST["orderby"] ) : 'created';
		$order    = ! empty( $_REQUEST["order"] ) ? esc_sql( $_REQUEST["order"] ) : ( empty( $_REQUEST['orderby'] ) ? 'DESC' : 'ASC' );
		if ( ! empty( $order_by ) && ! empty( $order ) ) {
			$query .= ' ORDER BY ' . $order_by . ' ' . $order;
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
			'action'              => __( 'Actions', MM_WPFS::L10N_DOMAIN_ADMIN ),
			'subscriber'          => $this->format_column_header_title( __( 'Subscriber', MM_WPFS::L10N_DOMAIN_ADMIN ), array(
				__( 'Name', MM_WPFS::L10N_DOMAIN_ADMIN ),
				__( 'E-mail', MM_WPFS::L10N_DOMAIN_ADMIN )
			) ),
			'subscription_plan'   => $this->format_column_header_title( __( 'Subscription', MM_WPFS::L10N_DOMAIN_ADMIN ), array(
				__( 'Plan', MM_WPFS::L10N_DOMAIN_ADMIN ),
				__( 'ID', MM_WPFS::L10N_DOMAIN_ADMIN )
			) ),
			'subscription_status' => $this->format_column_header_title( __( 'Subscription', MM_WPFS::L10N_DOMAIN_ADMIN ), array(
				__( 'Status', MM_WPFS::L10N_DOMAIN_ADMIN ),
				__( 'Mode', MM_WPFS::L10N_DOMAIN_ADMIN )
			) ),
			'billing'             => $this->format_column_header_title( __( 'Billing', MM_WPFS::L10N_DOMAIN_ADMIN ), array(
				__( 'Country', MM_WPFS::L10N_DOMAIN_ADMIN ),
				__( 'VAT', MM_WPFS::L10N_DOMAIN_ADMIN )
			) ),
			'created'             => $this->format_column_header_title( __( 'Created at', MM_WPFS::L10N_DOMAIN_ADMIN ), array(
				__( 'Form name', MM_WPFS::L10N_DOMAIN_ADMIN )
			) )
		);
	}

	protected function get_sortable_columns() {
		return array(
			'created' => array( 'created', false )
		);
	}

	public function display_rows() {
		$items = $this->items;

		list( $columns, $hidden ) = $this->get_column_info();

		if ( ! empty( $items ) ) {
			foreach ( $items as $item ) {
				$row = '';
				$row .= "<tr id=\"record_{$item->subscriberID}\">";
				foreach ( $columns as $column_name => $column_display_name ) {
					$class = "class=\"$column_name column-$column_name\"";
					$style = "";
					if ( in_array( $column_name, $hidden ) ) {
						$style = "style=\"display:none;\"";
					}
					$attributes = "{$class} {$style}";

					switch ( $column_name ) {
						case 'subscriber':
							$href                 = $this->build_stripe_customer_link( $item->stripeCustomerID, $item->livemode );
							$stripe_customer_link = "<a href=\"{$href}\" target=\"_blank\">{$item->email}</a>";
							$name                 = $item->name;
							if ( ! empty( $name ) ) {
								$name_label = stripslashes( $name );
							} else {
								$name_label = __( '&lt;Not specified&gt;', MM_WPFS::L10N_DOMAIN_ADMIN );
							}
							$row .= "<td {$attributes}><b>{$name_label}</b><br/>{$stripe_customer_link}</td>";
							break;
						case 'subscription_plan':
							$planAndQuantityLabel   = sprintf( '%d%s %s', $item->quantity, self::MULTIPLICATION_MARK, $item->planID );
							$href                   = $this->build_stripe_subscription_link( $item->stripeSubscriptionID, $item->livemode );
							$stripeSubscriptionLink = "<a href=\"{$href}\" target=\"_blank\">{$item->stripeSubscriptionID}</a>";
							$row .= "<td {$attributes}><b>{$planAndQuantityLabel}</b><br/>{$stripeSubscriptionLink}</td>";
							break;
						case 'subscription_status':
							$status_Label = ucfirst( $item->status );
							if ( $item->chargeMaximumCount > 0 ) {
								$status_Label = sprintf( "%s (%d/%d)", ucfirst( $item->status ), $item->chargeCurrentCount, $item->chargeMaximumCount );
							}
							$live_mode_label = $item->livemode == 0 ? __( 'Test', MM_WPFS::L10N_DOMAIN_ADMIN ) : __( 'Live', MM_WPFS::L10N_DOMAIN_ADMIN );
							$row .= "<td {$attributes}><b>{$status_Label}</b><br/>$live_mode_label</td>";
							break;
						case 'billing':
							$countryLabel    = empty( $item->addressCountry ) ? '' : trim( $item->addressCountry );
							$vatPercentLabel = '';
							if ( isset( $item->vatPercent ) && $item->vatPercent != 0 ) {
								$vatPercentLabel = sprintf( '%s%%', round( $item->vatPercent, 4 ) );
							}
							$row .= "<td {$attributes}><b>{$countryLabel}</b><br/>{$vatPercentLabel}</td>";
							break;
						case 'created':
							$create_at_label = date( 'F jS Y H:i', strtotime( $item->created ) );
							$form_name_label = esc_html( $item->formName );
							$row .= "<td {$attributes}><b>$create_at_label</b><br/>$form_name_label</td>";
							break;
						case 'action':
							$row .= "<td {$attributes}>";
							if ( $item->status == 'cancelled' || $item->status == 'ended' ) {
								$row .= "<button class=\"button delete\" data-id=\"{$item->subscriberID}\" data-type=\"subscription_record\" title=\"" . __( 'Delete', MM_WPFS::L10N_DOMAIN_ADMIN ) . "\" data-confirm=\"true\"><i class=\"fa fa-trash-o fa-fw\"></i></button>";
							} else {
								$row .= "<button class=\"button delete\" data-id=\"{$item->subscriberID}\" data-type=\"subscriber\" title=\"" . __( 'Cancel', MM_WPFS::L10N_DOMAIN_ADMIN ) . "\"><i class=\"fa fa-ban fa-fw\"></i></button>";
							}
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

/**
 * Created by PhpStorm.
 * User: codex
 * Date: 2020.08.25.
 * Time: 14:56
 */
class WPFS_Multiple_Donations_Table extends WPFS_Base_Table {

    const MULTIPLICATION_MARK = 'x';

    public function __construct() {
        parent::__construct( array(
            'singular' => __( 'Donation', MM_WPFS::L10N_DOMAIN_ADMIN ),
            'plural'   => __( 'Donations', MM_WPFS::L10N_DOMAIN_ADMIN ),
            'ajax'     => false
        ) );
    }

    public function no_items() {
        _e( 'No donations found.', MM_WPFS::L10N_DOMAIN_ADMIN );
    }

    public function prepare_items() {
        global $wpdb;

        $query = "SELECT donationID, stripeCustomerID, stripeSubscriptionID, stripePaymentIntentID, paid, captured, refunded, expired, lastChargeStatus, subscriptionStatus, currency, amount, donationFrequency, name, email, created, cancelled, livemode, formId, formName FROM {$wpdb->prefix}fullstripe_donations";

        $where_statement = null;

        $donor   = ! empty( $_REQUEST["donor"] ) ? esc_sql( trim( $_REQUEST["donor"] ) ) : null;
        $donation = ! empty( $_REQUEST["donation"] ) ? esc_sql( trim( $_REQUEST["donation"] ) ) : null;
        $mode         = ! empty( $_REQUEST["mode"] ) ? esc_sql( trim( $_REQUEST["mode"] ) ) : null;

        if ( isset( $donor ) ) {
            if ( ! isset( $where_statement ) ) {
                $where_statement = ' WHERE ';
            } else {
                $where_statement .= ' AND ';
            }
            $where_statement .= sprintf( "(LOWER(name) LIKE LOWER('%s') OR LOWER(email) LIKE LOWER('%s') OR stripeCustomerID LIKE '%s')", "%$donor%", "%$donor%", "%$donor%" );
        }

        if ( isset( $donation ) ) {
            if ( ! isset( $where_statement ) ) {
                $where_statement = ' WHERE ';
            } else {
                $where_statement .= ' AND ';
            }
            $where_statement .= sprintf( "( stripeSubscriptionID LIKE '%s' or stripePaymentIntentID like '%s')", "%$donation%", "%$donation%"  );
        }

        if ( isset( $mode ) ) {
            if ( ! isset( $where_statement ) ) {
                $where_statement = ' WHERE ';
            } else {
                $where_statement .= ' AND ';
            }
            $where_statement .= sprintf( '(livemode = %d)', $mode == 'live' ? 1 : 0 );
        }

        if ( isset( $where_statement ) ) {
            $query .= $where_statement;
        }

        $order_by = ! empty( $_REQUEST["orderby"] ) ? esc_sql( $_REQUEST["orderby"] ) : 'created';
        $order    = ! empty( $_REQUEST["order"] ) ? esc_sql( $_REQUEST["order"] ) : ( empty( $_REQUEST['orderby'] ) ? 'DESC' : 'ASC' );
        if ( ! empty( $order_by ) && ! empty( $order ) ) {
            $query .= ' ORDER BY ' . $order_by . ' ' . $order;
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
            'action'              => __( 'Actions', MM_WPFS::L10N_DOMAIN_ADMIN ),
            'donor'          => $this->format_column_header_title( __( 'Donor', MM_WPFS::L10N_DOMAIN_ADMIN ), array(
                __( 'Name', MM_WPFS::L10N_DOMAIN_ADMIN ),
                __( 'E-mail', MM_WPFS::L10N_DOMAIN_ADMIN )
            ) ),
            'donation' => $this->format_column_header_title( __( 'Donation', MM_WPFS::L10N_DOMAIN_ADMIN ), array(
                __( 'Amount', MM_WPFS::L10N_DOMAIN_ADMIN ),
                __( 'Frequency', MM_WPFS::L10N_DOMAIN_ADMIN )
            ) ),
            'payment2'             => $this->format_column_header_title( __( 'Payment', MM_WPFS::L10N_DOMAIN_ADMIN ), array(
                __( 'Status', MM_WPFS::L10N_DOMAIN_ADMIN ),
                __( 'Mode', MM_WPFS::L10N_DOMAIN_ADMIN )
            ) ),
            'payment'   => $this->format_column_header_title( __( 'Payment', MM_WPFS::L10N_DOMAIN_ADMIN ), array(
                __( 'Form name, Date', MM_WPFS::L10N_DOMAIN_ADMIN ),
                __( 'IDs', MM_WPFS::L10N_DOMAIN_ADMIN )
            ) )
        );
    }

    protected function get_sortable_columns() {
        return array(
            'created' => array( 'created', false )
        );
    }

    protected function generateDonationFrequencyLabel( $donationFrequency ) {
        $label = "Unknown";
        switch ( $donationFrequency ) {
            case MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_ONE_TIME:
                $label = __( "One-time", MM_WPFS::L10N_DOMAIN_PUBLIC );
                break;

            case MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_DAILY:
                $label = __( "Daily", MM_WPFS::L10N_DOMAIN_PUBLIC );
                break;

            case MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_WEEKLY:
                $label = __( "Weekly", MM_WPFS::L10N_DOMAIN_PUBLIC );
                break;

            case MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_MONTHLY:
                $label = __( "Monthly", MM_WPFS::L10N_DOMAIN_PUBLIC );
                break;

            case MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_ANNUAL:
                $label = __( "Annual", MM_WPFS::L10N_DOMAIN_PUBLIC );
                break;
        }

        return $label;
    }

    protected function getDonationStatusLabel( $item, $status ) {
        $statusLabel = __( "Unknown", MM_WPFS::L10N_DOMAIN_PUBLIC );

        if ( MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_ONE_TIME == $item->donationFrequency ) {
            if ( MM_WPFS::PAYMENT_STATUS_PAID === $status ) {
                $statusLabel = __( "Paid", MM_WPFS::L10N_DOMAIN_PUBLIC );
            } else if ( MM_WPFS::PAYMENT_STATUS_REFUNDED === $status  ) {
                $statusLabel = __( "Refunded", MM_WPFS::L10N_DOMAIN_PUBLIC );
            }
        } else {
            if ( $item->subscriptionStatus === 'active' ) {
                $statusLabel = __( "Running", MM_WPFS::L10N_DOMAIN_PUBLIC );
            } else if ( $item->subscriptionStatus === MM_WPFS::SUBSCRIBER_STATUS_CANCELLED &&
                        MM_WPFS::PAYMENT_STATUS_PAID === $status ) {
                $statusLabel = __( "Paid", MM_WPFS::L10N_DOMAIN_PUBLIC );
            } else if ( $item->subscriptionStatus === MM_WPFS::SUBSCRIBER_STATUS_CANCELLED &&
                        MM_WPFS::PAYMENT_STATUS_REFUNDED === $status ) {
                $statusLabel = __( "Refunded", MM_WPFS::L10N_DOMAIN_PUBLIC );
            }
        }

        return $statusLabel;
    }

    private function isDonationCancelledAndRefunded( $item, $status ) {
        $res = $item->donationFrequency == MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_ONE_TIME ?
                                            MM_WPFS::PAYMENT_STATUS_REFUNDED === $status :
                                            MM_WPFS::PAYMENT_STATUS_REFUNDED === $status && $item->subscriptionStatus === MM_WPFS::SUBSCRIBER_STATUS_CANCELLED;
                                           
        return $res;
    }


    public function display_rows() {
        $items = $this->items;

        list( $columns, $hidden ) = $this->get_column_info();

        if ( ! empty( $items ) ) {
            foreach ( $items as $item ) {
                $status = MM_WPFS_Utils::getDonationStatus( $item );
                $row = '';
                $row .= "<tr id=\"record_{$item->stripePaymentIntentID}\">";
                foreach ( $columns as $column_name => $column_display_name ) {
                    $class = "class=\"$column_name column-$column_name\"";
                    $style = "";
                    if ( in_array( $column_name, $hidden ) ) {
                        $style = "style=\"display:none;\"";
                    }
                    $attributes = "{$class} {$style}";

                    switch ( $column_name ) {
                        case 'donor':
                            $href                 = $this->build_stripe_customer_link( $item->stripeCustomerID, $item->livemode );
                            $stripe_customer_link = "<a href=\"{$href}\" target=\"_blank\">{$item->email}</a>";
                            $name                 = $item->name;
                            if ( ! empty( $name ) ) {
                                $name_label = stripslashes( $name );
                            } else {
                                $name_label = __( '&lt;Not specified&gt;', MM_WPFS::L10N_DOMAIN_ADMIN );
                            }
                            $row .= "<td {$attributes}><b>{$name_label}</b><br/>{$stripe_customer_link}</td>";
                            break;

                        case 'payment':
                            $createdAtLabel = date( 'F jS Y H:i', strtotime( $item->created ) );
                            $donationUrl = $this->build_stripe_payments_link( $item->stripePaymentIntentID, $item->livemode );
                            $recurringURL = null;
                            if ( !is_null( $item->stripeSubscriptionID ) ) {
                                $recurringURL = $this->build_stripe_subscription_link( $item->stripeSubscriptionID, $item->livemode );
                            }
                            $donationPart = "<a href=\"{$donationUrl}\" target=\"_blank\">{$item->stripePaymentIntentID}</a>";
                            $subscriptionPart = is_null( $recurringURL ) ? "" :  ", <a href=\"{$recurringURL}\" target=\"_blank\">{$item->stripeSubscriptionID}</a>";
                            $row .= "<td {$attributes}><b>{$item->formName}, {$createdAtLabel}</b><br/>{$donationPart}{$subscriptionPart}</td>";
                            break;

                        case 'donation':
                            $amountLabel = MM_WPFS_Currencies::formatAndEscapeByAdmin( $item->currency, $item->amount, false, true );
                            $donationFrequencyLabel = $this->generateDonationFrequencyLabel( $item->donationFrequency );
                            $row .= "<td {$attributes}><b>{$amountLabel}</b><br/>{$donationFrequencyLabel}</td>";
                            break;

                        case 'payment2':
                            $statusLabel   = $this->getDonationStatusLabel( $item, $status );
                            $liveModeLabel = $item->livemode == 0 ? __( 'Test', MM_WPFS::L10N_DOMAIN_ADMIN ) : __( 'Live', MM_WPFS::L10N_DOMAIN_ADMIN );
                            $row .= "<td {$attributes}><b>{$statusLabel}</b><br/>{$liveModeLabel}</td>";
                            break;

                        case 'action':
                            $row .= "<td {$attributes}>";
                            if ( MM_WPFS::PAYMENT_STATUS_PAID === $status ) {
                                $row .= "<button class=\"button button-primary action\" data-id=\"{$item->donationID}\" data-type=\"donationPayment\" data-operation=\"refund\" title=\"" . __( 'Refund', MM_WPFS::L10N_DOMAIN_ADMIN ) . "\"><i class=\"fa fa-undo fa-fw\"></i></button>";
                                $row .= ' ';
                            }
                            if ( $item->subscriptionStatus === 'active' ) {
                                $row .= "<button class=\"button button-primary action\" data-id=\"{$item->donationID}\" data-type=\"donationSubscription\" data-operation=\"cancel\" title=\"" . __( 'Cancel', MM_WPFS::L10N_DOMAIN_ADMIN ) . "\"><i class=\"fa fa-ban fa-fw\"></i></button>";
                                $row .= ' ';
                            }
                            if ( $this->isDonationCancelledAndRefunded( $item, $status )) {
                                $row .= "<button class=\"button delete\" data-id=\"{$item->donationID}\" data-type=\"donationRecord\" title=\"" . __( 'Delete', MM_WPFS::L10N_DOMAIN_ADMIN ) . "\" data-confirm=\"true\"><i class=\"fa fa-trash-o fa-fw\"></i></button>";
                            }
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

/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2016.09.01.
 * Time: 12:54
 */
class WPFS_Named_Payments_Table extends WPFS_Base_Table {

	public function __construct() {
		parent::__construct( array(
			'singular' => __( 'Payment', MM_WPFS::L10N_DOMAIN_ADMIN ),
			'plural'   => __( 'Payments', MM_WPFS::L10N_DOMAIN_ADMIN ),
			'ajax'     => false
		) );
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	public function prepare_items() {
		global $wpdb;

		$query = "SELECT paymentID,eventID,description,payment_method,paid,captured,refunded,expired,failure_code,failure_message,livemode,last_charge_status,currency,amount,fee,addressLine1,addressLine2,addressCity,addressState,addressZip,addressCountry,created,stripeCustomerID,name,email,formId,formType,formName FROM {$wpdb->prefix}fullstripe_payments";

		$where_statement = null;

		$customer       = ! empty( $_REQUEST['customer'] ) ? esc_sql( trim( $_REQUEST['customer'] ) ) : null;
		$payment        = ! empty( $_REQUEST['payment'] ) ? esc_sql( trim( $_REQUEST['payment'] ) ) : null;
		$payment_status = ! empty( $_REQUEST['status'] ) ? esc_sql( trim( $_REQUEST['status'] ) ) : null;
		$mode           = ! empty( $_REQUEST['mode'] ) ? esc_sql( trim( $_REQUEST['mode'] ) ) : null;

		if ( isset( $customer ) ) {
			if ( ! isset( $where_statement ) ) {
				$where_statement = ' WHERE ';
			} else {
				$where_statement .= ' AND ';
			}
			$where_statement .= sprintf( "(LOWER(name) LIKE LOWER('%s') OR LOWER(email) LIKE LOWER('%s') OR stripeCustomerID LIKE '%s')", "%$customer%", "%$customer%", "%$customer%" );
		}

		if ( isset( $payment ) ) {
			if ( ! isset( $where_statement ) ) {
				$where_statement = ' WHERE ';
			} else {
				$where_statement .= ' AND ';
			}
			$where_statement .= sprintf( "(eventID LIKE '%s')", "%$payment%" );
		}

		if ( isset( $payment_status ) ) {
			if ( ! isset( $where_statement ) ) {
				$where_statement = ' WHERE ';
			} else {
				$where_statement .= ' AND ';
			}
			if ( MM_WPFS::PAYMENT_STATUS_FAILED === $payment_status ) {
				$where_statement .= sprintf( "last_charge_status='%s'", MM_WPFS::STRIPE_CHARGE_STATUS_FAILED );
			} elseif ( MM_WPFS::PAYMENT_STATUS_PENDING === $payment_status ) {
				$where_statement .= sprintf( "last_charge_status='%s'", MM_WPFS::STRIPE_CHARGE_STATUS_PENDING );
			} elseif ( MM_WPFS::PAYMENT_STATUS_EXPIRED === $payment_status ) {
				$where_statement .= sprintf( "expired=%d", 1 );
			} elseif ( MM_WPFS::PAYMENT_STATUS_REFUNDED === $payment_status ) {
				$where_statement .= sprintf( "refunded=%d", 1 );
			} elseif ( MM_WPFS::PAYMENT_STATUS_RELEASED === $payment_status ) {
				$where_statement .= sprintf( "(refunded=%d AND captured=%d)", 1, 0 );
			} elseif ( MM_WPFS::PAYMENT_STATUS_PAID === $payment_status ) {
				$where_statement .= sprintf( "(last_charge_status='%s' AND paid=%d AND captured=%d AND expired=%d AND refunded=%d)", MM_WPFS::STRIPE_CHARGE_STATUS_SUCCEEDED, 1, 1, 0, 0 );
			} elseif ( MM_WPFS::PAYMENT_STATUS_AUTHORIZED === $payment_status ) {
				$where_statement .= sprintf( "(last_charge_status='%s' AND paid=%d AND captured=%d AND expired=%d AND refunded=%d)", MM_WPFS::STRIPE_CHARGE_STATUS_SUCCEEDED, 1, 0, 0, 0 );
			}
		}

		if ( isset( $mode ) ) {
			if ( ! isset( $where_statement ) ) {
				$where_statement = ' WHERE ';
			} else {
				$where_statement .= ' AND ';
			}
			$where_statement .= sprintf( '(livemode = %d)', $mode == 'live' ? 1 : 0 );
		}

		if ( isset( $where_statement ) ) {
			$query .= $where_statement;
		}

		$order_by = ! empty( $_REQUEST["orderby"] ) ? esc_sql( $_REQUEST["orderby"] ) : 'created';
		$order    = ! empty( $_REQUEST["order"] ) ? esc_sql( $_REQUEST["order"] ) : ( empty( $_REQUEST['orderby'] ) ? 'DESC' : 'ASC' );
		if ( ! empty( $order_by ) && ! empty( $order ) ) {
			$query .= ' ORDER BY ' . $order_by . ' ' . $order;
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

	/**
	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
	 */
	public function get_columns() {
		return array(
			'action'         => __( 'Actions', MM_WPFS::L10N_DOMAIN_ADMIN ),
			'customer'       => $this->format_column_header_title( __( 'Customer', MM_WPFS::L10N_DOMAIN_ADMIN ), array(
				__( 'Name', MM_WPFS::L10N_DOMAIN_ADMIN ),
				__( 'E-mail', MM_WPFS::L10N_DOMAIN_ADMIN )
			) ),
			'payment'        => $this->format_column_header_title( __( 'Payment', MM_WPFS::L10N_DOMAIN_ADMIN ), array(
				__( 'Amount', MM_WPFS::L10N_DOMAIN_ADMIN ),
				__( 'ID', MM_WPFS::L10N_DOMAIN_ADMIN )
			) ),
			'payment_status' => $this->format_column_header_title( __( 'Status', MM_WPFS::L10N_DOMAIN_ADMIN ), array(
				__( 'Mode', MM_WPFS::L10N_DOMAIN_ADMIN )
			) ),
			'created'        => $this->format_column_header_title( __( 'Date', MM_WPFS::L10N_DOMAIN_ADMIN ), array(
				__( 'Form name', MM_WPFS::L10N_DOMAIN_ADMIN )
			) )
		);
	}

	/**
	 * Decide which columns to activate the sorting functionality on
	 * @return array $sortable, the array of columns that can be sorted by the user
	 */
	protected function get_sortable_columns() {
		return array(
			'created' => array( 'created', false )
		);
	}

	/**
	 * Display the rows of records in the table
	 * @return string, echo the markup of the rows
	 */
	public function display_rows() {
		$items = $this->items;

		list( $columns, $hidden ) = $this->get_column_info();

		if ( ! empty( $items ) ) {
			foreach ( $items as $item ) {
				$payment_status = MM_WPFS_Utils::get_payment_status( $item );
				$action_count   = 0;
				$row            = '';
				$row .= "<tr id=\"record_{$item->paymentID}\">";
				foreach ( $columns as $column_name => $column_display_name ) {
					$class = "class=\"$column_name column-$column_name\"";
					$style = "";
					if ( in_array( $column_name, $hidden ) ) {
						$style = " style=\"display:none;\"";
					}
					$attributes = "{$class} {$style}";

					switch ( $column_name ) {
						case "customer":
							$href                 = $this->build_stripe_customer_link( $item->stripeCustomerID, $item->livemode );
							$stripe_customer_link = "<a href=\"{$href}\" target=\"_blank\">{$item->email}</a>";
							$name                 = $item->name;
							if ( ! empty( $name ) ) {
								$name_label = stripslashes( $name );
							} else {
								$name_label = __( '&lt;Not specified&gt;', MM_WPFS::L10N_DOMAIN_ADMIN );
							}
							$row .= "<td {$attributes}><b>{$name_label}</b><br/>{$stripe_customer_link}</td>";
							break;
						case "payment":
							$href               = $this->build_stripe_payments_link( $item->eventID, $item->livemode );
							$stripe_charge_link = "<a href=\"{$href}\" target=\"_blank\">{$item->eventID}</a>";
							$amount_label       = MM_WPFS_Currencies::format( $item->currency, $item->amount );
							$row .= "<td {$attributes}><b>{$amount_label}</b><br/>{$stripe_charge_link}</td>";
							break;
						case "payment_status":
							$payment_status_label = MM_WPFS_Admin::getPaymentStatusLabel($payment_status);
							$live_mode_label      = $item->livemode == 0 ? __( 'Test', MM_WPFS::L10N_DOMAIN_ADMIN ) : __( 'Live', MM_WPFS::L10N_DOMAIN_ADMIN );
							$row .= "<td $attributes><b>$payment_status_label</b><br/>$live_mode_label</td>";
							break;
						case "created":
							$date_label      = date( 'F jS Y H:i', strtotime( $item->created ) );
							$form_name_label = esc_html( $item->formName );
							$row .= "<td {$attributes}><b>$date_label</b><br/>$form_name_label</td>";
							break;
						case "action":
							$row .= "<td {$attributes}>";
							if ( MM_WPFS::PAYMENT_STATUS_AUTHORIZED === $payment_status ) {
								$action_count ++;
								$row .= "<button class=\"button button-primary action\" data-type=\"payment\" data-operation=\"capture\" data-id=\"{$item->paymentID}\" title=\"" . __( 'Capture', MM_WPFS::L10N_DOMAIN_ADMIN ) . "\"><i class=\"fa fa-credit-card fa-fw\"></i></button>";
							}
							$row .= ' ';
							if ( MM_WPFS::PAYMENT_STATUS_PAID === $payment_status ) {
								$action_count ++;
								$row .= "<button class=\"button button-primary action\" data-type=\"payment\" data-operation=\"refund\" data-id=\"{$item->paymentID}\" title=\"" . __( 'Refund', MM_WPFS::L10N_DOMAIN_ADMIN ) . "\"><i class=\"fa fa-undo fa-fw\"></i></button>";
							}
							if ( MM_WPFS::PAYMENT_STATUS_AUTHORIZED === $payment_status ) {
								$action_count ++;
								$row .= "<button class=\"button button-primary action\" data-type=\"payment\" data-operation=\"release\" data-id=\"{$item->paymentID}\" title=\"" . __( 'Release', MM_WPFS::L10N_DOMAIN_ADMIN ) . "\"><i class=\"fa fa-undo fa-fw\"></i></button>";
							}
							if ( $action_count > 0 ) {
								$row .= "<span class=\"form-action-last\">";
							}
							$row .= "<button class=\"button delete\" data-id=\"{$item->paymentID}\" data-type=\"payment\" title=\"" . __( 'Delete (local)', MM_WPFS::L10N_DOMAIN_ADMIN ) . "\"><i class=\"fa fa-trash-o fa-fw\"></i></button>";
							if ( $action_count > 0 ) {
								$row .= "</span>";
							}
							$row .= "</td>";
							break;
					}


				}


				$row .= '</tr>';

				echo $row;
			}
		}
	}

	public function no_items() {
		_e( 'No payments found.', MM_WPFS::L10N_DOMAIN_ADMIN );
	}

}

class WPFS_Card_Captures_Table extends WPFS_Base_Table {

	public function __construct() {
		parent::__construct( array(
			'singular' => __( 'Saved card', MM_WPFS::L10N_DOMAIN_ADMIN ),
			'plural'   => __( 'Saved cards', MM_WPFS::L10N_DOMAIN_ADMIN ),
			'ajax'     => false
		) );
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	public function prepare_items() {
		global $wpdb;

		$query = "SELECT * FROM {$wpdb->prefix}fullstripe_card_captures";

		$where_statement = null;

		$customer = ! empty( $_REQUEST["customer"] ) ? esc_sql( trim( $_REQUEST["customer"] ) ) : null;
		$mode     = ! empty( $_REQUEST["mode"] ) ? esc_sql( trim( $_REQUEST["mode"] ) ) : null;

		if ( isset( $customer ) ) {
			if ( ! isset( $where_statement ) ) {
				$where_statement = ' WHERE ';
			} else {
				$where_statement .= ' AND ';
			}
			$where_statement .= sprintf( "( LOWER( name ) LIKE LOWER( '%s' ) OR LOWER( email ) LIKE LOWER( '%s' ) OR stripeCustomerID LIKE '%s')", "%$customer%", "%$customer%", "%$customer%" );
		}

		if ( isset( $mode ) ) {
			if ( ! isset( $where_statement ) ) {
				$where_statement = ' WHERE ';
			} else {
				$where_statement .= ' AND ';
			}
			$where_statement .= sprintf( '(livemode = %d)', $mode == 'live' ? 1 : 0 );
		}

		if ( isset( $where_statement ) ) {
			$query .= $where_statement;
		}

		$order_by = ! empty( $_REQUEST["orderby"] ) ? esc_sql( $_REQUEST["orderby"] ) : 'created';
		$order    = ! empty( $_REQUEST["order"] ) ? esc_sql( $_REQUEST["order"] ) : ( empty( $_REQUEST['orderby'] ) ? 'DESC' : 'ASC' );
		if ( ! empty( $order_by ) && ! empty( $order ) ) {
			$query .= ' ORDER BY ' . $order_by . ' ' . $order;
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

	/**
	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
	 */
	public function get_columns() {
		return array(
			'action'   => __( 'Actions', MM_WPFS::L10N_DOMAIN_ADMIN ),
			'customer' => $this->format_column_header_title( __( 'Customer', MM_WPFS::L10N_DOMAIN_ADMIN ), array(
				__( 'Name', MM_WPFS::L10N_DOMAIN_ADMIN ),
				__( 'E-mail', MM_WPFS::L10N_DOMAIN_ADMIN )
			) ),
			'mode'     => $this->format_column_header_title( __( 'Mode', MM_WPFS::L10N_DOMAIN_ADMIN ) ),
			'created'  => $this->format_column_header_title( __( 'Date', MM_WPFS::L10N_DOMAIN_ADMIN ), array(
				__( 'Form name', MM_WPFS::L10N_DOMAIN_ADMIN )
			) )
		);
	}

	/**
	 * Decide which columns to activate the sorting functionality on
	 * @return array $sortable, the array of columns that can be sorted by the user
	 */
	protected function get_sortable_columns() {
		return array(
			'created' => array( 'created', false )
		);
	}

	/**
	 * Display the rows of records in the table
	 * @return string, echo the markup of the rows
	 */
	public function display_rows() {
		$items = $this->items;

		list( $columns, $hidden ) = $this->get_column_info();

		if ( ! empty( $items ) ) {
			foreach ( $items as $item ) {
				$row = '';
				$row .= "<tr id=\"record_{$item->captureID}\">";
				foreach ( $columns as $column_name => $column_display_name ) {
					$class = "class=\"$column_name column-$column_name\"";
					$style = "";
					if ( in_array( $column_name, $hidden ) ) {
						$style = " style=\"display:none;\"";
					}
					$attributes = "{$class} {$style}";

					switch ( $column_name ) {
						case "customer":
							$href                 = $this->build_stripe_customer_link( $item->stripeCustomerID, $item->livemode );
							$stripe_customer_link = "<a href=\"{$href}\" target=\"_blank\">{$item->email}</a>";
							$name                 = $item->name;
							if ( ! empty( $name ) ) {
								$name_label = stripslashes( $name );
							} else {
								$name_label = __( '&lt;Not specified&gt;', MM_WPFS::L10N_DOMAIN_ADMIN );
							}
							$row .= "<td {$attributes}><b>{$name_label}</b><br/>{$stripe_customer_link}</td>";
							break;
						case "mode":
							$live_mode_label = $item->livemode == 0 ? __( 'Test', MM_WPFS::L10N_DOMAIN_ADMIN ) : __( 'Live', MM_WPFS::L10N_DOMAIN_ADMIN );
							$row .= "<td $attributes><b>$live_mode_label</b></td>";
							break;
						case "created":
							$date_label      = date( 'F jS Y H:i', strtotime( $item->created ) );
							$form_name_label = esc_html( $item->formName );
							$row .= "<td {$attributes}><b>$date_label</b><br/>$form_name_label</td>";
							break;
						case "action":
							$row .= "<td {$attributes}><button class=\"button delete\" data-id=\"{$item->captureID}\" data-type=\"cardCapture\" title=\"" . __( 'Delete (local)', MM_WPFS::L10N_DOMAIN_ADMIN ) . "\"><i class=\"fa fa-trash-o fa-fw\"></i></button></td>";
							break;
					}

				}

				$row .= '</tr>';

				echo $row;
			}
		}
	}

	public function no_items() {
		_e( 'No saved cards found.', MM_WPFS::L10N_DOMAIN_ADMIN );
	}

}

class WPFS_Log_Table extends WPFS_Base_Table {

	public function __construct() {
		parent::__construct( array(
			'singular' => __( 'Log entry', MM_WPFS::L10N_DOMAIN_ADMIN ),
			'plural'   => __( 'Log entries', MM_WPFS::L10N_DOMAIN_ADMIN ),
			'ajax'     => false
		) );
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	public function prepare_items() {
		global $wpdb;

		$query = "SELECT * FROM {$wpdb->prefix}fullstripe_log";

		$whereStatement = null;

		$module = ! empty( $_REQUEST['module'] ) ? esc_sql( trim( $_REQUEST['module'] ) ) : null;
		$level  = ! empty( $_REQUEST['level'] ) ? esc_sql( trim( $_REQUEST['level'] ) ) : null;

		if ( isset( $module ) ) {
			if ( ! isset( $whereStatement ) ) {
				$whereStatement = ' WHERE ';
			} else {
				$whereStatement .= ' AND ';
			}
			$whereStatement .= sprintf( "( LOWER( `module` ) = LOWER( '%s' ) )", $module );
		}

		if ( isset( $level ) ) {
			if ( ! isset( $whereStatement ) ) {
				$whereStatement = ' WHERE ';
			} else {
				$whereStatement .= ' AND ';
			}
			$whereStatement .= sprintf( '(`level` = %s)', $level );
		}

		if ( isset( $whereStatement ) ) {
			$query .= $whereStatement;
		}

		$orderBy = ! empty( $_REQUEST['orderby'] ) ? esc_sql( $_REQUEST['orderby'] ) : 'created';
		$order   = ! empty( $_REQUEST['order'] ) ? esc_sql( $_REQUEST['order'] ) : ( empty( $_REQUEST['orderby'] ) ? 'DESC' : 'ASC' );
		if ( ! empty( $orderBy ) && ! empty( $order ) ) {
			$query .= ' ORDER BY ' . $orderBy . ' ' . $order;
		}

		$total_items = $wpdb->query( $query );
		$per_page    = 50;
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

	/**
	 * Decide which columns to activate the sorting functionality on
	 * @return array $sortable, the array of columns that can be sorted by the user
	 */
	protected function get_sortable_columns() {
		return array(
			'created' => array( 'created', false )
		);
	}


	/**
	 * Display the rows of records in the table
	 * @return string, echo the markup of the rows
	 */
	public function display_rows() {
		$items = $this->items;

		list( $columns, $hidden ) = $this->get_column_info();

		if ( ! empty( $items ) ) {
			foreach ( $items as $item ) {
				$row = '';
				$row .= "<tr id=\"record_{$item->id}\">";
				foreach ( $columns as $column_name => $column_display_name ) {
					$class = "class=\"$column_name column-$column_name\"";
					$style = "";
					if ( in_array( $column_name, $hidden ) ) {
						$style = " style=\"display:none;\"";
					}
					$attributes = "{$class} {$style}";

					switch ( $column_name ) {
						case 'created':
							$dateLabel = date( 'F jS Y H:i', strtotime( $item->created ) );
							$row .= "<td {$attributes}>{$dateLabel}</td>";
							break;
						case 'module':
							$module = $item->module;
							$row .= "<td {$attributes}>{$module}</td>";
							break;
						case 'class':
							$class = $item->class;
							$row .= "<td {$attributes}>{$class}</td>";
							break;
						case 'function':
							$function = $item->function;
							$row .= "<td {$attributes}>{$function}</td>";
							break;
						case 'level':
							$level = $item->level;
							$row .= "<td {$attributes}>{$level}</td>";
							break;
						case 'message':
							$message    = $item->message;
							$stackTrace = $item->exception;
							$row .= "<td {$attributes} data-toggle=\"{$stackTrace}\">{$message}</td>";
							break;
					}
				}

				$row .= '</tr>';

				echo $row;
			}
		}
	}

	public function no_items() {
		_e( 'No log entries found.', MM_WPFS::L10N_DOMAIN_ADMIN );
	}

}
