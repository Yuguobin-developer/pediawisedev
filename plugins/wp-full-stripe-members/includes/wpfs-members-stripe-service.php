<?php

/**
 * Service class to interact with Stripe API
 */
class MM_WPFS_Members_Stripe_Service {

	/**
	 * Retrieves a non-deleted customer from Stripe by customer ID
	 *
	 * @param $stripe_customer_id
	 *
	 * @return null|\StripeWPFS\Customer
	 */
	public function retrieve_active_customer( $stripe_customer_id ) {
		$stripe_customer = $this->retrieve_customer( $stripe_customer_id );

		// tnagy check if customer is not deleted
		if ( isset( $stripe_customer ) && isset( $stripe_customer->deleted ) && $stripe_customer->deleted ) {
			return null;
		}

		return $stripe_customer;
	}

	/**
	 * Retrieves a customer from Stripe by customer ID
	 *
	 * @param $stripe_customer_id
	 *
	 * @return null|\StripeWPFS\Customer
	 */
	public function retrieve_customer( $stripe_customer_id ) {
		try {
			return \StripeWPFS\Customer::retrieve( $stripe_customer_id );
		} catch ( Exception $e ) {

			$this->handle_error( $e );

			return null;
		}
	}

	/**
	 * Handle error silently
	 *
	 * @param $e
	 */
	private function handle_error( Exception $e ) {
		if ( isset( $e ) ) {
			$error_message = sprintf( 'Message=%s, Stack=%s', $e->getMessage(), $e->getTraceAsString() );
			error_log( $error_message );
		}
	}

	/**
	 * Retrieves a Stripe customer's subscription by the given id if exists
	 *
	 * @param $stripe_customer
	 * @param $subscription_id
	 *
	 * @return null|\StripeWPFS\Subscription
	 */
	public function retrieve_customer_subscription(\StripeWPFS\Customer $stripe_customer, $subscription_id ) {
		if ( isset( $stripe_customer->subscriptions ) ) {
			try {
				return $stripe_customer->subscriptions->retrieve( $subscription_id );
			} catch ( Exception $e ) {
				$this->handle_error( $e );

				return null;
			}
		}

		return null;
	}

    /**
     * Retrieves a Stripe subscription with the plan's product expanded
     *
     * @param $subscription_id
     *
     * @return null|\StripeWPFS\Subscription
     */
    public function retrieve_subscription($subscription_id ) {
        try {
            return \StripeWPFS\Subscription::retrieve( array( "id" => $subscription_id, "expand" => array( "plan.product" ) ) );
        } catch ( Exception $e ) {
            $this->handle_error( $e );

            return null;
        }

        return null;
    }

    /**
     * Retrieves a plan from Stripe by ID
     *
     * @param $plan_id
     *
     * @return null|\StripeWPFS\Plan
     */
    public function retrieve_plan( $plan_id ) {
        try {
            return \StripeWPFS\Plan::retrieve( $plan_id );
        } catch ( Exception $e ) {
            $this->handle_error( $e );

            return null;
        }
    }

    /**
     * Updates a subscription to be cancelled at the end of the current period.
     *
     * @param $stripeSubscriptionID
     */
    public function cancelSubscriptionAtPeriodEnd( $stripeSubscriptionID ) {
        try {
            \StripeWPFS\Subscription::update(
                $stripeSubscriptionID,
                array (
                    'cancel_at_period_end' => true
                )
            );
        } catch ( Exception $e ) {
            $this->handle_error( $e );
        }
    }
}