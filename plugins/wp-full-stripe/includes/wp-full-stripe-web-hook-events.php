<?php

/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2018.06.15.
 * Time: 13:25
 */
class MM_WPFS_EventHandler {

	/** @var $db MM_WPFS_Database */
	protected $db = null;
	/** @var $stripe MM_WPFS_Stripe */
	protected $stripe = null;
	/** @var $mailer MM_WPFS_Mailer */
	protected $mailer = null;
	/** @var array */
	protected $eventProcessors = array();
	/** @var MM_WPFS_LoggerService */
	private $loggerService;
	/** @var MM_WPFS_Logger */
	private $logger;

	/**
	 * MM_WPFS_WebHookEventHandler constructor.
	 *
	 * @param MM_WPFS_Database $db
	 * @param MM_WPFS_Stripe $stripe
	 * @param MM_WPFS_Mailer $mailer
	 * @param MM_WPFS_LoggerService $loggerService
	 */
	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		$this->db            = $db;
		$this->stripe        = $stripe;
		$this->mailer        = $mailer;
		$this->loggerService = $loggerService;
		$this->logger        = $this->loggerService->createWebHookEventHandlerLogger( MM_WPFS_EventHandler::class );
		$this->initProcessors();
	}

	protected function initProcessors() {
		$processors = array(
			new MM_WPFS_CustomerSubscriptionDeleted( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_InvoiceCreated( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_InvoicePaymentSucceeded( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_ChargeCaptured( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_ChargeExpired( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_ChargeFailed( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_ChargePending( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_ChargeRefunded( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_ChargeSucceeded( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_ChargeUpdated( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_CustomerSubscriptionUpdated( $this->db, $this->stripe, $this->mailer, $this->loggerService )
		);
		foreach ( $processors as $processor ) {
			$this->eventProcessors[ $processor->get_type() ] = $processor;
		}
	}

	public function handle( $event ) {
		try {
			$eventProcessed = false;
			if ( isset( $event ) && isset( $event->type ) ) {
				$eventProcessor = null;
				if ( array_key_exists( $event->type, $this->eventProcessors ) ) {
					$eventProcessor = $this->eventProcessors[ $event->type ];
				}
				if ( $eventProcessor instanceof MM_WPFS_EventProcessor ) {
					$eventProcessor->process( $event );
					$eventProcessed = true;
				}
			}

			return $eventProcessed;
		} catch ( Exception $e ) {
			$this->logger->error( __FUNCTION__, $e->getMessage(), $e );
			throw $e;
		}
	}

}

abstract class MM_WPFS_EventProcessor {

	const STRIPE_API_VERSION_2018_02_28 = '2018-02-28';
	const STRIPE_API_VERSION_2018_05_21 = '2018-05-21';

	/* @var MM_WPFS_LoggerService */
	protected $loggerService;
	/* @var $db MM_WPFS_Database */
	protected $db = null;
	/* @var $stripe MM_WPFS_Stripe */
	protected $stripe = null;
	/* @var $mailer MM_WPFS_Mailer */
	protected $mailer = null;
	/* @var boolean */
	protected $debugLog = false;

	/**
	 * MM_WPFS_WebHookEventProcessor constructor.
	 *
	 * @param MM_WPFS_Database $db
	 * @param MM_WPFS_Stripe $stripe
	 * @param MM_WPFS_Mailer $mailer
	 * @param MM_WPFS_LoggerService $loggerService
	 */
	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		$this->db            = $db;
		$this->stripe        = $stripe;
		$this->mailer        = $mailer;
		$this->loggerService = $loggerService;
	}

	public final function process( $event_object ) {
		if ( $this->get_type() === $event_object->type ) {
			$this->process_event( $event_object );
		}
	}

	public abstract function get_type();

	protected function process_event( $event ) {
		// tnagy default implementation, override in subclasses
	}

	/**
	 * @param \StripeWPFS\Event $event
	 *
	 * @return null|\StripeWPFS\ApiResource
	 */
	protected function get_data_object( $event ) {
		$object = null;
		if ( isset( $event ) && isset( $event->data ) && isset( $event->data->object ) ) {
			$object = $event->data->object;
		}

		return $object;
	}

	/**
	 * Adds an event ID to a JSON encoded array if the ID is not in the array
	 *
	 * @param string $encodedStripeEventIDs JSON encoded event ID array
	 * @param \StripeWPFS\Event $stripeEvent
	 * @param bool $success output variable to determine whether the event ID has been added to the array
	 *
	 * @return string the new JSON encoded array
	 */
	protected function insertIfNotExists( $encodedStripeEventIDs, $stripeEvent, &$success ) {
		$decodedStripeEventIDs = json_decode( $encodedStripeEventIDs );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			$decodedStripeEventIDs = array();
		}
		if ( ! is_array( $decodedStripeEventIDs ) ) {
			$decodedStripeEventIDs = array();
		}
		if ( isset( $stripeEvent ) && isset( $stripeEvent->id ) ) {
			if ( in_array( $stripeEvent->id, $decodedStripeEventIDs ) ) {
				$data    = $encodedStripeEventIDs;
				$success = false;
			} else {
				array_push( $decodedStripeEventIDs, $stripeEvent->id );
				$data = json_encode( $decodedStripeEventIDs );
				if ( json_last_error() === JSON_ERROR_NONE ) {
					$success = true;
				} else {
					$success = false;
				}
			}
		} else {
			$data    = $encodedStripeEventIDs;
			$success = false;
		}

		return $data;
	}

	/**
	 * @param \StripeWPFS\Event $event
	 *
	 * @return null|array
	 */
	protected function get_data_previous_attributes( $event ) {
		$previous_attributes = null;
		if ( isset( $event ) && isset( $event->data ) && isset( $event->data->previous_attributes ) ) {
			$previous_attributes = $event->data->previous_attributes;
		}

		return $previous_attributes;
	}

    /**
     * @param $wpfsSubscriber
     * @return array|object|void|null
     */
    protected function getSubscriptionFormBySubscriber( $wpfsSubscriber ) {
        $form = $this->db->get_subscription_form_by_id( $wpfsSubscriber->formId );
        if ( $form->name === $wpfsSubscriber->formName ) {
            return $form;
        }
        $form = $this->db->get_checkout_subscription_form_by_id( $wpfsSubscriber->formId );
        if ( $form->name === $wpfsSubscriber->formName ) {
            return $form;
        }

        return null;
    }

    /**
     * @param $wpfsSubscriber
     */
    protected function sendSubscriptionEndedReceipt( $wpfsSubscriber ) {
        $form = $this->getSubscriptionFormBySubscriber( $wpfsSubscriber );
        $sendReceipt = MM_WPFS_Utils::isSendingPluginEmailByForm( $form );

        if ( $sendReceipt ) {
            $transactionData = $this->createSubscriptionDataBySubscriber( $wpfsSubscriber );
            $this->mailer->sendSubscriptionFinishedEmailReceipt( $form, $transactionData );
        }
    }

    protected function createSubscriptionDataBySubscriber( $wpfsSubscriber ) {
        $stripePlan = $this->stripe->retrieve_plan($wpfsSubscriber->planID);
        if (isset($stripePlan)) {
            $stripePlanSetupFee = MM_WPFS_Utils::get_setup_fee_for_plan($stripePlan);

            $countryComposite = MM_WPFS_Countries::get_country_by_name($wpfsSubscriber->addressCountry);
            $billingAddress = MM_WPFS_Utils::prepare_address_data($wpfsSubscriber->addressLine1, $wpfsSubscriber->addressLine2, $wpfsSubscriber->addressCity, $wpfsSubscriber->addressState, $wpfsSubscriber->addressCountry, is_null($countryComposite) ? '' : $countryComposite['alpha-2'], $wpfsSubscriber->addressZip);
            $shippingAddress = MM_WPFS_Utils::prepare_address_data($wpfsSubscriber->shippingAddressLine1, $wpfsSubscriber->shippingAddressLine2, $wpfsSubscriber->shippingAddressCity, $wpfsSubscriber->shippingAddressState, $wpfsSubscriber->shippingAddressCountry, is_null($countryComposite) ? '' : $countryComposite['alpha-2'], $wpfsSubscriber->addressZip);

            $planAmountGrossComposite = MM_WPFS_Utils::calculateGrossFromNet($stripePlan->amount, $wpfsSubscriber->vatPercent);
            $planSetupFeeGrossComposite = MM_WPFS_Utils::calculateGrossFromNet($stripePlanSetupFee, $wpfsSubscriber->vatPercent);
            $planAmountGrossTotalComposite = MM_WPFS_Utils::calculateGrossFromNet($wpfsSubscriber->quantity * $stripePlan->amount, $wpfsSubscriber->vatPercent);
            $planSetupFeeGrossTotalComposite = MM_WPFS_Utils::calculateGrossFromNet($wpfsSubscriber->quantity * $stripePlanSetupFee, $wpfsSubscriber->vatPercent);

            $transactionData = new MM_WPFS_SubscriptionTransactionData();

            $transactionData->setFormName($wpfsSubscriber->formName);
            $transactionData->setStripeCustomerId($wpfsSubscriber->stripeCustomerID);
            $transactionData->setCustomerName($wpfsSubscriber->name);
            $transactionData->setCustomerEmail($wpfsSubscriber->email);
            $transactionData->setPlanId($wpfsSubscriber->planID);
            $transactionData->setPlanName($stripePlan->product->name);
            $transactionData->setPlanCurrency($stripePlan->currency);
            $transactionData->setPlanNetSetupFee($stripePlanSetupFee);
            $transactionData->setPlanGrossSetupFee($planSetupFeeGrossComposite['gross']);
            $transactionData->setPlanSetupFeeVAT($transactionData->getPlanGrossSetupFee() - $transactionData->getPlanNetSetupFee());
            $transactionData->setPlanSetupFeeVATRate($wpfsSubscriber->vatPercent);
            $transactionData->setPlanNetSetupFeeTotal($planSetupFeeGrossTotalComposite['net']);
            $transactionData->setPlanGrossSetupFeeTotal($planSetupFeeGrossTotalComposite['gross']);
            $transactionData->setPlanSetupFeeVATTotal($planSetupFeeGrossTotalComposite['taxValue']);
            $transactionData->setPlanNetAmount($stripePlan->amount);
            $transactionData->setPlanGrossAmount($planAmountGrossComposite['gross']);
            $transactionData->setPlanAmountVAT($transactionData->getPlanGrossAmount() - $transactionData->getPlanNetAmount());
            $transactionData->setPlanAmountVATRate($wpfsSubscriber->vatPercent);
            $transactionData->setPlanQuantity($wpfsSubscriber->quantity);
            $transactionData->setPlanNetAmountTotal($planAmountGrossTotalComposite['net']);
            $transactionData->setPlanGrossAmountTotal($planAmountGrossTotalComposite['gross']);
            $transactionData->setPlanAmountVATTotal($planAmountGrossTotalComposite['taxValue']);
            $transactionData->setProductName($stripePlan->product->name);
            $transactionData->setBillingName($wpfsSubscriber->billingName);
            $transactionData->setBillingAddress($billingAddress);
            $transactionData->setShippingName($wpfsSubscriber->shippingName);
            $transactionData->setShippingAddress($shippingAddress);
            $transactionData->setTransactionId( $wpfsSubscriber->stripeSubscriptionID );

            /*
             *          IMPORTANT: The following TransactionData attributes are not set at the moment:
             *          - Coupon code
             *          - Metadata
             *          - Custom input values
             *          - Subscription description
             *          - Billing cycle anchor day
             *          - Prorate until anchor day
             */

            return $transactionData;
        }

        return null;
    }

    /**
     * @param $stripeSubscriptionId
     *
     * @return array|null|object|void
     */
    protected function findSubscriberByStripeSubscriptionId( $stripeSubscriptionId ) {
        return $this->db->getSubscriptionByStripeSubscriptionId($stripeSubscriptionId );
    }

}

abstract class MM_WPFS_InvoiceEventProcessor extends MM_WPFS_EventProcessor {

	const INVOICE_ITEM_TYPE_SUBSCRIPTION = 'subscription';

	protected function findSubscriptionIdInLine( $event, $line ) {
		$stripe_subscription_id        = null;
		$stripe_subscription_id_source = null;
		if ( strtotime( self::STRIPE_API_VERSION_2018_05_21 ) <= strtotime( $event->api_version ) ) {
			if ( self::INVOICE_ITEM_TYPE_SUBSCRIPTION === $line->type ) {
				$stripe_subscription_id        = $line->subscription;
				$stripe_subscription_id_source = 'subscription';
			}
		} else {
			$stripe_subscription_id        = $line->id;
			$stripe_subscription_id_source = 'id';
		}

		if ( $this->getLogger()->isDebugEnabled() ) {
			$this->getLogger()->debug( __FUNCTION__, "api_version={$event->api_version}, stripe_subscription_id={$stripe_subscription_id}, stripe_subscription_id_source={$stripe_subscription_id_source}" );
		}

		return $stripe_subscription_id;
	}

	/**
	 * @return MM_WPFS_Logger
	 */
	protected abstract function getLogger();

	/**
	 * @param \StripeWPFS\Event $event
	 * @param \StripeWPFS\InvoiceLineItem $line
	 *
	 * @return null
	 */
	protected function findSubmitHashInLine( $event, $line ) {
		$submitHash = null;
		if ( strtotime( self::STRIPE_API_VERSION_2018_05_21 ) <= strtotime( $event->api_version ) ) {
			if ( self::INVOICE_ITEM_TYPE_SUBSCRIPTION === $line->type ) {
				if ( isset( $line->metadata ) && isset( $line->metadata->client_reference_id ) ) {
					$submitHash = $line->metadata->client_reference_id;
				}
			}
		}

		return $submitHash;
	}

    /**
     * @param $submitHash
     *
     * @return array|null|object|void
     */
    protected function findPopupFormSubmitByHash( $submitHash ) {
        return $this->db->find_popup_form_submit_by_hash( $submitHash );
    }

    /**
     * @param $popupFormSubmit
     * @param \StripeWPFS\Event $stripeEvent
     *
     * @return bool|int
     */
    protected function updatePopupFormSubmitWithEvent( $popupFormSubmit, $stripeEvent ) {
        if ( isset( $popupFormSubmit->relatedStripeEventIDs ) ) {
            $encodedStripeEventIDs = $popupFormSubmit->relatedStripeEventIDs;
        } else {
            $encodedStripeEventIDs = null;
        }
        $inserted              = false;
        $relatedStripeEventIDs = $this->insertIfNotExists( $encodedStripeEventIDs, $stripeEvent, $inserted );
        if ( $inserted ) {
            if ( $this->logger->isDebugEnabled() ) {
                $this->logger->debug( __FUNCTION__, 'MM_WPFS_InvoicePaymentSucceeded::updatePopupFormSubmitWithEvent(): ' . sprintf( 'Updating PopupFormSubmit \'%s\' with event ID \'%s\'', $popupFormSubmit->hash, $stripeEvent->id ) );
            }

            return $this->db->updatePopupFormSubmitWithEvent( $popupFormSubmit->hash, $relatedStripeEventIDs );
        }

        return false;
    }

    /**
     * @param $wpfsSubscriber
     */
    protected function endSubscription( $wpfsSubscriber ) {
        $success = $this->stripe->cancel_subscription( $wpfsSubscriber->stripeCustomerID, $wpfsSubscriber->stripeSubscriptionID );
        if ( $success ) {
            $this->db->endSubscription( $wpfsSubscriber->stripeSubscriptionID );

            $this->sendSubscriptionEndedReceipt( $wpfsSubscriber );
        }
    }

    /**
     * @param $wpfsSubscriber
     * @param $stripeEvent
     *
     * @return bool|int
     */
    protected function updateSubscriberWithPaymentAndEvent( $wpfsSubscriber, $stripeEvent ) {
        if ( isset( $wpfsSubscriber->processedEventIDs ) ) {
            $encodedStripeEventIDs = $wpfsSubscriber->processedEventIDs;
        } else {
            $encodedStripeEventIDs = null;
        }
        $inserted                = false;
        $processedStripeEventIDs = $this->insertIfNotExists( $encodedStripeEventIDs, $stripeEvent, $inserted );
        if ( $inserted ) {
            return $this->db->updateSubscriberWithPaymentAndEvent( $wpfsSubscriber->stripeSubscriptionID, $processedStripeEventIDs );
        }

        return false;
    }

    /**
     * @param $wpfsSubscriber
     * @param $stripeEvent
     *
     * @return bool|int
     */
    protected function updateSubscriberWithInvoiceAndEvent( $wpfsSubscriber, $stripeEvent ) {
        if ( isset( $wpfsSubscriber->processedEventIDs ) ) {
            $encodedStripeEventIDs = $wpfsSubscriber->processedEventIDs;
        } else {
            $encodedStripeEventIDs = null;
        }
        $inserted                = false;
        $processedStripeEventIDs = $this->insertIfNotExists( $encodedStripeEventIDs, $stripeEvent, $inserted );
        if ( $inserted ) {
            return $this->db->updateSubscriberWithInvoiceAndEvent( $wpfsSubscriber->stripeSubscriptionID, $processedStripeEventIDs );
        }

        return false;
    }

    /**
     * @param $wpfsSubscriber
     * @param \StripeWPFS\Event $stripeEvent
     * @param bool $inserted
     * @return string
     */
    protected function addProcessedWebhookEvent( $wpfsSubscriber, $stripeEvent, &$inserted ) {
        if ( isset( $wpfsSubscriber->processedStripeEventIDs )) {
            $encodedStripeEventIDs = $wpfsSubscriber->processedStripeEventIDs;
        } else {
            $encodedStripeEventIDs = null;
        }
        $processedStripeEventIDs = $this->insertIfNotExists( $encodedStripeEventIDs, $stripeEvent, $inserted );

        return $processedStripeEventIDs;
    }

}

class MM_WPFS_CustomerSubscriptionDeleted extends MM_WPFS_EventProcessor {

	/* @var MM_WPFS_Logger */
	private $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::CUSTOMER_SUBSCRIPTION_DELETED;
	}

    protected function isDonationSubscription($subscriptionId ) {
        $isDonation = false;

        $donationRecord = $this->db->getDonationByStripeSubscriptionId( $subscriptionId );
        if ( ! is_null( $donationRecord )) {
            $isDonation = true;
        }

        if ( ! $isDonation ) {
            $subscriptionRecord = $this->db->getSubscriptionByStripeSubscriptionId( $subscriptionId );
            if ( is_null( $subscriptionRecord )) {
                MM_WPFS_Utils::log( __CLASS__ . "." . __FUNCTION__ . "(): Cannot find subscription record with id '{$subscriptionId}'." );
            }
        }

        return $isDonation;
    }


    protected function updateSubscriptionToCancelled($event, $stripeSubscription ) {
        $wpfsSubscriber = $this->findSubscriberByStripeSubscriptionId( $stripeSubscription->id );

        if ( isset( $wpfsSubscriber ) ) {
            if ( MM_WPFS::SUBSCRIPTION_STATUS_ENDED     !== $wpfsSubscriber->status &&
                MM_WPFS::SUBSCRIPTION_STATUS_CANCELLED !== $wpfsSubscriber->status ) {
                $this->updateSubscriberWithEvent( $wpfsSubscriber, $event );
                $wpfsSubscriber = $this->findSubscriberByStripeSubscriptionId( $stripeSubscription->id );

                if ( $wpfsSubscriber->chargeMaximumCount > 0 ) {
                    if ( $wpfsSubscriber->chargeCurrentCount >= $wpfsSubscriber->chargeMaximumCount ) {
                        $this->manageEndedSubscription( $wpfsSubscriber, $stripeSubscription->id );
                    } else {
                        $this->db->cancelSubscriptionByStripeSubscriptionId( $stripeSubscription->id );
                    }
                } else {
                    $this->db->cancelSubscriptionByStripeSubscriptionId( $stripeSubscription->id );
                }
            }
        }
    }

    protected function updateDonationToCancelled($stripeSubscription ) {
        $wpfsDonation = $this->db->getDonationByStripeSubscriptionId( $stripeSubscription->id );

        if ( isset( $wpfsDonation ) ) {
            if ( MM_WPFS::SUBSCRIPTION_STATUS_CANCELLED !== $wpfsDonation->subscriptionStatus ) {
                $this->db->cancelDonationByStripeSubscriptionId( $stripeSubscription->id );
            }
        }
    }

    protected function process_event( $event ) {
		$stripeSubscription = $this->get_data_object( $event );

		if ( ! is_null( $stripeSubscription ) ) {
		    if ( $this->isDonationSubscription( $stripeSubscription->id ) ) {
                $this->updateDonationToCancelled( $stripeSubscription);
            } else {
                $this->updateSubscriptionToCancelled( $event, $stripeSubscription);
            }
		}
	}

    private function updateSubscriberWithEvent($wpfsSubscriber, $stripeEvent ) {
        if ( isset( $wpfsSubscriber->processedEventIDs ) ) {
            $encodedStripeEventIDs = $wpfsSubscriber->processedEventIDs;
        } else {
            $encodedStripeEventIDs = null;
        }
        $inserted                = false;
        $processedStripeEventIDs = $this->insertIfNotExists( $encodedStripeEventIDs, $stripeEvent, $inserted );
        if ( $inserted ) {
            return $this->db->updateSubscriberWithEvent( $wpfsSubscriber->stripeSubscriptionID, $processedStripeEventIDs );
        }

        return false;
    }

    /**
     * @param $wpfsSubscriber
     */
    protected function manageEndedSubscription( $wpfsSubscriber, $stripeSubscriptionId ) {
        $this->db->endSubscription( $stripeSubscriptionId );
        $this->sendSubscriptionEndedReceipt( $wpfsSubscriber );
    }

}

class MM_WPFS_InvoicePaymentSucceeded extends MM_WPFS_InvoiceEventProcessor {

	/* @var MM_WPFS_Logger */
	protected $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::INVOICE_PAYMENT_SUCCEEDED;
	}

	protected function getLogger() {
		return $this->logger;
	}

	protected function process_event( $event ) {
		foreach ( $event->data->object->lines->data as $line ) {
			$wpfsSubscriber       = null;
			$stripeSubscriptionId = $this->findSubscriptionIdInLine( $event, $line );
			if ( $this->logger->isDebugEnabled() ) {
				$this->logger->debug( __FUNCTION__, "stripe_subscription_id=$stripeSubscriptionId" );
			}
			if ( ! is_null( $stripeSubscriptionId ) ) {
				$wpfsSubscriber = $this->findSubscriberByStripeSubscriptionId( $stripeSubscriptionId );
				if ( isset( $wpfsSubscriber ) ) {
					if (
						MM_WPFS::SUBSCRIBER_STATUS_ENDED     !== $wpfsSubscriber->status &&
						MM_WPFS::SUBSCRIBER_STATUS_CANCELLED !== $wpfsSubscriber->status
					) {
						$this->updateSubscriberWithPaymentAndEvent( $wpfsSubscriber, $event );
                        $wpfsSubscriber = $this->findSubscriberByStripeSubscriptionId( $stripeSubscriptionId );

                        if ( $wpfsSubscriber->chargeMaximumCount > 0 ) {
                            if ( $wpfsSubscriber->chargeCurrentCount > $wpfsSubscriber->chargeMaximumCount ) {
                                $this->endSubscription( $wpfsSubscriber );
                            } else {
                                if ( $this->logger->isDebugEnabled() ) {
                                    $this->logger->debug( __FUNCTION__, 'subscription charged until maximum charge reached' );
                                }
                            }
                        } else {
                            if ( $this->logger->isDebugEnabled() ) {
                                $this->logger->debug( __FUNCTION__, "subscription->chargeMaximumCount is zero" );
                            }
                        }
					} else {
						if ( $this->logger->isDebugEnabled() ) {
							$this->logger->debug( __FUNCTION__, "subscription status is 'ended' or 'cancelled', skip" );
						}
					}
				} else {
					if ( $this->logger->isDebugEnabled() ) {
						$this->logger->debug( __FUNCTION__, 'subscription not found, try to find PopupFormSubmit entry...' );
					}
					$submitHash = $this->findSubmitHashInLine( $event, $line );
					if ( $this->logger->isDebugEnabled() ) {
						$this->logger->debug( __FUNCTION__, 'submitHash=' . "$submitHash" );
					}
					if ( ! is_null( $submitHash ) ) {
						$popupFormSubmit = $this->findPopupFormSubmitByHash( $submitHash );
						if ( $this->logger->isDebugEnabled() ) {
							$this->logger->debug( __FUNCTION__, 'popupFormSubmit=' . print_r( $popupFormSubmit, true ) );
						}
						if ( ! is_null( $popupFormSubmit ) ) {
							$this->updatePopupFormSubmitWithEvent( $popupFormSubmit, $event );
							if ( $this->logger->isDebugEnabled() ) {
								$this->logger->debug( __FUNCTION__, 'popupFormSubmit updated with event ID' );
							}
						}
					}
				}
			}
		}
	}
}

class MM_WPFS_InvoiceCreated extends MM_WPFS_InvoiceEventProcessor {

	/* @var MM_WPFS_Logger */
	protected $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::INVOICE_CREATED;
	}

	protected function getLogger() {
		return $this->logger;
	}

	protected function process_event( $event ) {
		foreach ( $event->data->object->lines->data as $line ) {
			$wpfsSubscriber         = null;
			$stripe_subscription_id = $this->findSubscriptionIdInLine( $event, $line );
			if ( $this->logger->isDebugEnabled() ) {
				$this->logger->debug( __FUNCTION__, "stripe_subscription_id=$stripe_subscription_id" );
			}
			if ( ! is_null( $stripe_subscription_id ) ) {
				$wpfsSubscriber = $this->findSubscriberByStripeSubscriptionId( $stripe_subscription_id );
				if ( isset( $wpfsSubscriber ) ) {
					if (
						MM_WPFS::SUBSCRIBER_STATUS_ENDED !== $wpfsSubscriber->status
						&& MM_WPFS::SUBSCRIBER_STATUS_CANCELLED !== $wpfsSubscriber->status
					) {
                        $this->updateSubscriberWithInvoiceAndEvent( $wpfsSubscriber, $event );
                        $wpfsSubscriber = $this->findSubscriberByStripeSubscriptionId( $stripe_subscription_id );

						if ( $wpfsSubscriber->chargeMaximumCount > 0 ) {
							if ( $wpfsSubscriber->chargeCurrentCount >= $wpfsSubscriber->chargeMaximumCount &&
                                 $wpfsSubscriber->invoiceCreatedCount > $wpfsSubscriber->chargeMaximumCount ) {
								$this->endSubscription( $wpfsSubscriber );
							} else {
								if ( $this->logger->isDebugEnabled() ) {
									$this->logger->debug( __FUNCTION__, 'subscription charged until maximum charge reached' );
								}
							}
						} else {
							if ( $this->logger->isDebugEnabled() ) {
								$this->logger->debug( __FUNCTION__, "subscription->chargeMaximumCount is zero" );
							}
						}
					} else {
						if ( $this->logger->isDebugEnabled() ) {
							$this->logger->debug( __FUNCTION__, "subscription status is 'ended' or 'cancelled', skip" );
						}
					}
				} else {
                    if ( $this->logger->isDebugEnabled() ) {
                        $this->logger->debug( __FUNCTION__, 'subscription not found, try to find PopupFormSubmit entry...' );
                    }
                    $submitHash = $this->findSubmitHashInLine( $event, $line );
                    if ( $this->logger->isDebugEnabled() ) {
                        $this->logger->debug( __FUNCTION__, 'submitHash=' . "$submitHash" );
                    }
                    if ( ! is_null( $submitHash ) ) {
                        $popupFormSubmit = $this->findPopupFormSubmitByHash( $submitHash );
                        if ( $this->logger->isDebugEnabled() ) {
                            $this->logger->debug( __FUNCTION__, 'popupFormSubmit=' . print_r( $popupFormSubmit, true ) );
                        }
                        if ( ! is_null( $popupFormSubmit ) ) {
                            $this->updatePopupFormSubmitWithEvent( $popupFormSubmit, $event );
                            if ( $this->logger->isDebugEnabled() ) {
                                $this->logger->debug( __FUNCTION__, 'popupFormSubmit updated with event ID' );
                            }
                        }
                    }
				}
			}
		}
	}
}

trait MM_WPFS_ChargeEventUtils {
    protected function isDonationPayment( $paymentIntentId ) {
        $isDonation = false;

        $donationRecord = $this->db->getDonationByPaymentIntentId( $paymentIntentId );
        if ( ! is_null( $donationRecord )) {
            $isDonation = true;
        }

        if ( ! $isDonation ) {
            $paymentRecord = $this->db->getPaymentByEventId( $paymentIntentId );
            if ( is_null( $paymentRecord )) {
                MM_WPFS_Utils::log( __CLASS__ . "." . __FUNCTION__ . "(): Cannot find payment record with id '{$paymentIntentId}'." );
            }
        }

        return $isDonation;
    }

    /**
     * @param $charge \StripeWPFS\ApiResource
     */
    protected function updatePaymentStatus( $charge ) {
        if ( $this->isDonationPayment( $charge->payment_intent ) ) {
            $this->db->updateDonationByPaymentIntentId(
                $charge->payment_intent,
                array(
                    'paid'               => $charge->paid,
                    'captured'           => $charge->captured,
                    'refunded'           => $charge->refunded,
                    'lastChargeStatus'   => $charge->status
                )
            );
        } else {
            $this->db->updatePaymentByEventId(
                $charge->payment_intent,
                array(
                    'paid'               => $charge->paid,
                    'captured'           => $charge->captured,
                    'refunded'           => $charge->refunded,
                    'last_charge_status' => $charge->status
                )
            );
        }
    }

    /**
     * @param $charge \StripeWPFS\ApiResource
     */
    protected function updatePaymentStatusAndExpiry( $charge ) {
        if ( $this->isDonationPayment( $charge->payment_intent ) ) {
            $this->db->updateDonationByPaymentIntentId(
                $charge->payment_intent,
                array(
                    'paid'               => $charge->paid,
                    'captured'           => $charge->captured,
                    'refunded'           => $charge->refunded,
                    'lastChargeStatus'   => $charge->status,
                    'expired'            => true
                )
            );
        } else {
            $this->db->updatePaymentByEventId(
                $charge->payment_intent,
                array(
                    'paid'               => $charge->paid,
                    'captured'           => $charge->captured,
                    'refunded'           => $charge->refunded,
                    'last_charge_status' => $charge->status,
                    'expired'            => true
                )
            );
        }
    }

    /**
     * @param $charge \StripeWPFS\ApiResource
     */
    protected function updatePaymentStatusAndFailureCodes( $charge ) {
        if ( $this->isDonationPayment( $charge->payment_intent ) ) {
            $this->db->updateDonationByPaymentIntentId(
                $charge->payment_intent,
                array(
                    'paid'               => $charge->paid,
                    'captured'           => $charge->captured,
                    'refunded'           => $charge->refunded,
                    'lastChargeStatus'   => $charge->status,
					'failureCode'        => $charge->failure_code,
					'failureMessage'     => $charge->failure_message
                )
            );
        } else {
            $this->db->updatePaymentByEventId(
                $charge->payment_intent,
                array(
                    'paid'               => $charge->paid,
                    'captured'           => $charge->captured,
                    'refunded'           => $charge->refunded,
                    'last_charge_status' => $charge->status,
                    'failure_code'       => $charge->failure_code,
                    'failure_message'    => $charge->failure_message
                )
            );
        }
    }
}

class MM_WPFS_ChargeCaptured extends MM_WPFS_EventProcessor {
    use MM_WPFS_ChargeEventUtils;

	/* @var MM_WPFS_Logger */
	protected $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::CHARGE_CAPTURED;
	}

	protected function process_event( $event ) {
		$charge = $this->get_data_object( $event );
		if ( ! is_null( $charge ) ) {
		    $this->updatePaymentStatus( $charge );
		}
	}
}

class MM_WPFS_ChargeExpired extends MM_WPFS_EventProcessor {
    use MM_WPFS_ChargeEventUtils;

	/* @var MM_WPFS_Logger */
	protected $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::CHARGE_EXPIRED;
	}

	protected function process_event( $event ) {
		$charge = $this->get_data_object( $event );
		if ( ! is_null( $charge ) ) {
            $this->updatePaymentStatusAndExpiry( $charge );
		}
	}
}

class MM_WPFS_ChargeFailed extends MM_WPFS_EventProcessor {
    use MM_WPFS_ChargeEventUtils;

	/* @var MM_WPFS_Logger */
	protected $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::CHARGE_FAILED;
	}

	protected function process_event( $event ) {
		$charge = $this->get_data_object( $event );
		if ( ! is_null( $charge ) ) {
            $this->updatePaymentStatusAndFailureCodes( $charge );
		}
	}
}

class MM_WPFS_ChargePending extends MM_WPFS_EventProcessor {
    use MM_WPFS_ChargeEventUtils;

	/* @var MM_WPFS_Logger */
	protected $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::CHARGE_PENDING;
	}

	protected function process_event( $event ) {
		$charge = $this->get_data_object( $event );
		if ( ! is_null( $charge ) ) {
            $this->updatePaymentStatus( $charge );
		}
	}
}

class MM_WPFS_ChargeRefunded extends MM_WPFS_EventProcessor {
    use MM_WPFS_ChargeEventUtils;

	/* @var MM_WPFS_Logger */
	protected $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::CHARGE_REFUNDED;
	}

	protected function process_event( $event ) {
		$charge = $this->get_data_object( $event );
		if ( ! is_null( $charge ) ) {
            $this->updatePaymentStatus( $charge );
		}
	}
}

class MM_WPFS_ChargeSucceeded extends MM_WPFS_EventProcessor {
    use MM_WPFS_ChargeEventUtils;

	/* @var MM_WPFS_Logger */
	protected $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::CHARGE_SUCCEEDED;
	}

	protected function process_event( $event ) {
		$charge = $this->get_data_object( $event );
		if ( ! is_null( $charge ) ) {
            $this->updatePaymentStatus( $charge );
		}
	}
}

class MM_WPFS_ChargeUpdated extends MM_WPFS_EventProcessor {

	/* @var MM_WPFS_Logger */
	protected $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::CHARGE_UPDATED;
	}

	protected function process_event( $event ) {
		// tnagy charge description or metadata updated, nothing to do here
	}
}

class MM_WPFS_CustomerSubscriptionUpdated extends MM_WPFS_EventProcessor {

	public function get_type() {
		return \StripeWPFS\Event::CUSTOMER_SUBSCRIPTION_UPDATED;
	}

	protected function process_event( $event ) {
		$previous_attributes = $this->get_data_previous_attributes( $event );
		if ( ! is_null( $previous_attributes ) ) {
			/** @var \StripeWPFS\Subscription $stripe_subscription */
			$stripe_subscription = $this->get_data_object( $event );
			if ( ! is_null( $stripe_subscription ) ) {
				$wpfsSubscriber = $this->findSubscriberByStripeSubscriptionId( $stripe_subscription->id );
				if ( isset( $wpfsSubscriber ) ) {
					if ( array_key_exists( 'quantity', $previous_attributes ) ) {
						$this->db->update_subscriber(
							$wpfsSubscriber->subscriberID,
							array( 'quantity' => $stripe_subscription->quantity )
						);
					}
				}
			}
		}
	}

}