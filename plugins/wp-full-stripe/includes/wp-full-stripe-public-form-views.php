<?php

trait MM_WPFS_InlineFormView {

	/** @var MM_WPFS_Control */
	protected $cardHolderName;
	/** @var MM_WPFS_Control */
	protected $cardHolderEmail;
	/** @var MM_WPFS_Control */
	protected $card;

	public static function getInlineFields() {
		$fields = array(
			MM_WPFS_InlineFormViewConstants::FIELD_CARD_HOLDER_NAME  => MM_WPFS_ControlUtils::input( MM_WPFS_InlineFormViewConstants::FIELD_CARD_HOLDER_NAME ),
			MM_WPFS_InlineFormViewConstants::FIELD_CARD_HOLDER_EMAIL => MM_WPFS_ControlUtils::input( MM_WPFS_InlineFormViewConstants::FIELD_CARD_HOLDER_EMAIL ),
			MM_WPFS_InlineFormViewConstants::FIELD_CARD_NUMBER       => MM_WPFS_ControlUtils::cardInput( MM_WPFS_InlineFormViewConstants::FIELD_CARD_NUMBER )
		);

		return $fields;
	}

	public function setCurrentEmailAddress( $email_address ) {
		$this->cardHolderEmail()->setValue( $email_address );
	}

	public function cardHolderEmail() {
		return $this->cardHolderEmail;
	}

	public function cardHolderName() {
		return $this->cardHolderName;
	}

	public function card() {
		return $this->card;
	}

	protected function initInlineFields( $formHash ) {
		$this->cardHolderName  = MM_WPFS_ControlUtils::createControl( $formHash, MM_WPFS_InlineFormViewConstants::FIELD_CARD_HOLDER_NAME, null, null,
			/* translators: Form field label for card holder's name */
			__( 'Cardholder’s name', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
		$this->cardHolderEmail = MM_WPFS_ControlUtils::createControl( $formHash, MM_WPFS_InlineFormViewConstants::FIELD_CARD_HOLDER_EMAIL, null, null,
			/* translators: Form field label for email address */
			__( 'E-mail address', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
		$this->card            = MM_WPFS_ControlUtils::createControl( $formHash, MM_WPFS_InlineFormViewConstants::FIELD_CARD_NUMBER, null, null,
			/* translators: Form field label for credit card data */
			__( 'Card info', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
	}

	/**
	 * @return array
	 */
	protected function getInlineFormAttributes( $form ) {
		$attributes = array();

        $attributes[ MM_WPFS_InlineFormViewConstants::ATTR_DATA_WPFS_PREFERRED_LANGUAGE ]  = $form->preferredLanguage;

        return $attributes;
	}

}

trait MM_WPFS_PopupFormView {

	protected function getPopupFormAttributes( $form ) {
		$attributes = array();

		$attributes[ MM_WPFS_PopupFormViewConstants::ATTR_DATA_WPFS_COMPANY_NAME ]        = $form->companyName;
		$attributes[ MM_WPFS_PopupFormViewConstants::ATTR_DATA_WPFS_PRODUCT_DESCRIPTION ] = MM_WPFS_Localization::translateLabel( $form->productDesc );
		$attributes[ MM_WPFS_PopupFormViewConstants::ATTR_DATA_WPFS_BUTTON_TITLE ]        = MM_WPFS_Localization::translateLabel( $form->buttonTitle );
		$attributes[ MM_WPFS_PopupFormViewConstants::ATTR_DATA_WPFS_SHOW_REMEMBER_ME ]    = 0;
		$attributes[ MM_WPFS_PopupFormViewConstants::ATTR_DATA_WPFS_IMAGE ]               = $form->image;
		$attributes[ MM_WPFS_PopupFormViewConstants::ATTR_DATA_WPFS_USE_ALIPAY ]          = 0; // tnagy remove Alipay support temporarily $form->useAlipay
		$attributes[ MM_WPFS_PopupFormViewConstants::ATTR_DATA_WPFS_PREFERRED_LANGUAGE ]  = $form->preferredLanguage;

		return $attributes;
	}

}

interface MM_WPFS_FormViewConstants {

	const ATTR_ACTION = 'action';
	const ATTR_ID = 'id';
	const ATTR_CLASS = 'class';
	const ATTR_METHOD = 'method';
	const ATTR_VALUE = 'value';
	const ATTR_DATA_WPFS_FORM_ID = 'data-wpfs-form-id';
	const ATTR_DATA_WPFS_FORM_TYPE = 'data-wpfs-form-type';
	const ATTR_DATA_WPFS_FORM_HASH = 'data-wpfs-form-hash';
	const ATTR_DATA_WPFS_SHOW_ADDRESS = 'data-wpfs-show-address';
	const ATTR_DATA_WPFS_SHOW_BILLING_ADDRESS = 'data-wpfs-show-billing-address';
	const ATTR_DATA_WPFS_SHOW_SHIPPING_ADDRESS = 'data-wpfs-show-shipping-address';
	const ATTR_DATA_WPFS_CUSTOM_INPUT_TITLE = 'data-wpfs-custom-input-title';
	const ATTR_DATA_WPFS_CUSTOM_INPUTS = 'data-wpfs-custom-inputs';
	const ATTR_DATA_WPFS_CUSTOM_INPUT_REQUIRED = 'data-wpfs-custom-input-required';
	const ATTR_DATA_WPFS_SHOW_TERMS_OF_USE = 'data-wpfs-show-terms-of-use';
	const ATTR_DATA_WPFS_TERMS_OF_USE_NOT_CHECKED_ERROR_MESSAGE = 'data-wpfs-terms-of-use-not-checked-error-message';
	const ATTR_DATA_WPFS_CUSTOM_INPUT_FIELD = 'data-wpfs-custom-input-field';
	const ATTR_DATA_WPFS_CUSTOM_INPUT_LABEL = 'data-wpfs-custom-input-label';
	const ATTR_DATA_WPFS_VAT_RATE_TYPE = 'data-wpfs-vat-rate-type';
	const ATTR_DATA_WPFS_CURRENCY = 'data-wpfs-currency';
	const ATTR_DATA_WPFS_ZERO_DECIMAL_SUPPORT = 'data-wpfs-zero-decimal-support';
	const ATTR_DATA_WPFS_CURRENCY_SYMBOL = 'data-wpfs-currency-symbol';
	const ATTR_DATA_WPFS_AMOUNT = 'data-wpfs-amount';
	const ATTR_DATA_WPFS_AMOUNT_IN_SMALLEST_COMMON_CURRENCY = 'data-wpfs-amount-in-smallest-common-currency';
	const ATTR_DATA_WPFS_AMOUNT_INDEX = 'data-wpfs-amount-index';
	const ATTR_DATA_WPFS_AMOUNT_DESCRIPTION = 'data-wpfs-amount-description';
	const ATTR_DATA_WPFS_AMOUNT_TYPE = 'data-wpfs-amount-type';
	const ATTR_DATA_WPFS_ALLOW_LIST_OF_AMOUNTS_CUSTOM = 'data-wpfs-allow-list-of-amounts-custom';
	const ATTR_DATA_WPFS_AMOUNT_SELECTOR_STYLE = 'data-wpfs-amount-selector-style';
	const ATTR_DATA_WPFS_BUTTON_TITLE = 'data-wpfs-button-title';
	const ATTR_DATA_WPFS_SHOW_AMOUNT = 'data-wpfs-show-amount';
	const ATTR_DATA_WPFS_STEPPER = 'data-wpfs-stepper';
	const ATTR_DATA_WPFS_DECIMAL_SEPARATOR = 'data-wpfs-decimal-separator';
	const ATTR_DATA_WPFS_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE = 'data-wpfs-show-currency-symbol-instead-of-code';
	const ATTR_DATA_WPFS_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION = 'data-wpfs-show-currency-sign-at-first-position';
	const ATTR_DATA_WPFS_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT = 'data-wpfs-put-whitespace-between-currency-and-amount';

	const ATTR_DATA_DEFAULT_VALUE = 'data-default-value';
	const ATTR_DATA_MINIMUM_VALUE = 'data-min';
	const ATTR_DATA_MAXIMUM_VALUE = 'data-max';

	const ATTR_ID_VALUE_PREFIX = 'wpfs-form--';
	const ATTR_CLASS_VALUE_WPFS_FORM_WPFS_W_60 = 'wpfs-form wpfs-w-60';
	const ATTR_CLASS_VALUE_WPFS_FORM_WPFS_FORM_INLINE = 'wpfs-form wpfs-form--inline';
	const ATTR_METHOD_VALUE_POST = 'post';

	const FIELD_ACTION = 'action';
	const FIELD_FORM_NAME = 'wpfs-form-name';
	const FIELD_FORM_GET_PARAMETERS = 'wpfs-form-get-parameters';
	const FIELD_SAME_BILLING_AND_SHIPPING_ADDRESS = 'wpfs-same-billing-and-shipping-address';
	const FIELD_ADDRESS_SWITCHER = 'wpfs-address-switcher';
	const FIELD_BILLING_ADDRESS_PANEL = 'wpfs-billing-address-panel';
	const FIELD_BILLING_NAME = 'wpfs-billing-name';
	const FIELD_BILLING_ADDRESS_LINE1 = 'wpfs-billing-address-line-1';
	const FIELD_BILLING_ADDRESS_LINE2 = 'wpfs-billing-address-line-2';
	const FIELD_BILLING_ADDRESS_ZIP = 'wpfs-billing-address-zip';
	const FIELD_BILLING_ADDRESS_STATE = 'wpfs-billing-address-state';
	const FIELD_BILLING_ADDRESS_CITY = 'wpfs-billing-address-city';
	const FIELD_BILLING_ADDRESS_COUNTRY = 'wpfs-billing-address-country';
	const FIELD_SHIPPING_ADDRESS_PANEL = 'wpfs-shipping-address-panel';
	const FIELD_SHIPPING_NAME = 'wpfs-shipping-name';
	const FIELD_SHIPPING_ADDRESS_LINE1 = 'wpfs-shipping-address-line-1';
	const FIELD_SHIPPING_ADDRESS_LINE2 = 'wpfs-shipping-address-line-2';
	const FIELD_SHIPPING_ADDRESS_ZIP = 'wpfs-shipping-address-zip';
	const FIELD_SHIPPING_ADDRESS_STATE = 'wpfs-shipping-address-state';
	const FIELD_SHIPPING_ADDRESS_CITY = 'wpfs-shipping-address-city';
	const FIELD_SHIPPING_ADDRESS_COUNTRY = 'wpfs-shipping-address-country';
	const FIELD_CUSTOM_INPUT = 'wpfs-custom-input';
	const FIELD_TERMS_OF_USE_ACCEPTED = 'wpfs-terms-of-use-accepted';
	const FIELD_GOOGLE_RECAPTCHA_RESPONSE = 'g-recaptcha-response';
	const FIELD_NONCE = 'wpfs-nonce';

	const MACRO_SUBMIT_BUTTON_CAPTION_AMOUNT = '{{amount}}';

	const BUTTON_SUBMIT = 'submit';

}

interface MM_WPFS_PaymentFormViewConstants {

	const FIELD_CUSTOM_AMOUNT = 'wpfs-custom-amount';
	const FIELD_CUSTOM_AMOUNT_UNIQUE = 'wpfs-custom-amount-unique';

	const FIELD_ACTION_VALUE_INLINE_PAYMENT_CHARGE = 'wp_full_stripe_inline_payment_charge';
	const FIELD_ACTION_VALUE_POPUP_PAYMENT_CHARGE = 'wp_full_stripe_popup_payment_charge';

}

interface MM_WPFS_DonationFormViewConstants {
    const FIELD_CUSTOM_AMOUNT = 'wpfs-custom-amount';
    const FIELD_CUSTOM_AMOUNT_UNIQUE = 'wpfs-custom-amount-unique';
    const FIELD_DONATION_FREQUENCY = 'wpfs-donation-frequency';

    const FIELD_VALUE_DONATION_FREQUENCY_ONE_TIME = "one-time";
    const FIELD_VALUE_DONATION_FREQUENCY_DAILY = "daily";
    const FIELD_VALUE_DONATION_FREQUENCY_WEEKLY = "weekly";
    const FIELD_VALUE_DONATION_FREQUENCY_MONTHLY = "monthly";
    const FIELD_VALUE_DONATION_FREQUENCY_ANNUAL = "annual";

    const FIELD_ACTION_VALUE_INLINE_DONATION_CHARGE = 'wp_full_stripe_inline_donation_charge';
    const FIELD_ACTION_VALUE_POPUP_DONATION_CHARGE = 'wp_full_stripe_popup_donation_charge';
}

interface MM_WPFS_SubscriptionFormViewConstants {

	const FIELD_PLAN = 'wpfs-plan';
	const FIELD_PLAN_QUANTITY = 'wpfs-plan-quantity';
	const FIELD_COUPON = 'wpfs-coupon';

	const ATTR_DATA_WPFS_VALUE = 'data-wpfs-value';
	const ATTR_DATA_WPFS_PLAN_AMOUNT = 'data-wpfs-plan-amount';
	const ATTR_DATA_WPFS_PLAN_AMOUNT_IN_SMALLEST_COMMON_CURRENCY = 'data-wpfs-plan-amount-in-smallest-common-currency';
	const ATTR_DATA_WPFS_INTERVAL = 'data-wpfs-interval';
	const ATTR_DATA_WPFS_INTERVAL_COUNT = 'data-wpfs-interval-count';
	const ATTR_DATA_WPFS_CANCELLATION_COUNT = 'data-wpfs-cancellation-count';
	const ATTR_DATA_WPFS_PLAN_SETUP_FEE = 'data-wpfs-plan-setup-fee';
	const ATTR_DATA_WPFS_PLAN_SETUP_FEE_IN_SMALLEST_COMMON_CURRENCY = 'data-wpfs-plan-setup-fee-in-smallest-common-currency';
	const ATTR_DATA_WPFS_VAT_PERCENT = 'data-wpfs-vat-percent';
	const ATTR_DATA_WPFS_PLAN_NAME = 'data-wpfs-plan-name';
	const ATTR_DATA_WPFS_SIMPLE_BUTTON_LAYOUT = 'data-wpfs-simple-button-layout';

	const FIELD_ACTION_VALUE_INLINE_SUBSCRIPTION_CHARGE = 'wp_full_stripe_inline_subscription_charge';
	const FIELD_ACTION_VALUE_POPUP_SUBSCRIPTION_CHARGE = 'wp_full_stripe_popup_subscription_charge';

	const FIELD_CUSTOM_VAT_BILLING_COUNTRY = 'wpfs-custom-vat-billing-country';

}

interface MM_WPFS_InlineFormViewConstants {

	const FIELD_CARD_HOLDER_NAME = 'wpfs-card-holder-name';
	const FIELD_CARD_HOLDER_EMAIL = 'wpfs-card-holder-email';
	/**
	 * This is the default field name that Stripe Elements uses for Cards. It should be changed only in correlation with
	 * the Stripe Elements configuration.
	 */
	const FIELD_CARD_NUMBER = 'cardnumber';

    const ATTR_DATA_WPFS_PREFERRED_LANGUAGE = 'data-wpfs-preferred-language';

}

interface MM_WPFS_PopupFormViewConstants {

	const ATTR_DATA_WPFS_COMPANY_NAME = 'data-wpfs-company-name';
	const ATTR_DATA_WPFS_PRODUCT_DESCRIPTION = 'data-wpfs-product-description';
	const ATTR_DATA_WPFS_BUTTON_TITLE = 'data-wpfs-button-title';
	const ATTR_DATA_WPFS_SHOW_REMEMBER_ME = 'data-wpfs-show-remember-me';
	const ATTR_DATA_WPFS_IMAGE = 'data-wpfs-image';
	const ATTR_DATA_WPFS_USE_ALIPAY = 'data-wpfs-use-alipay';
	const ATTR_DATA_WPFS_PREFERRED_LANGUAGE = 'data-wpfs-preferred-language';

}

class MM_WPFS_ControlUtils {

	const FIELD_TYPE_INPUT = 'input';
	const FIELD_TYPE_INPUT_CUSTOM = 'input-custom';
	const FIELD_TYPE_INPUT_GROUP = 'input-group';
	const FIELD_TYPE_DROPDOWN = 'dropdown';
	const FIELD_TYPE_CHECKBOX = 'checkbox';
	const FIELD_TYPE_CARD = 'card';
	const FIELD_TYPE_CAPTCHA = 'captcha';

	public static final function customInput( $name ) {
		$class         = 'wpfs-form-control';
		$selector      = "#{fieldId}";
		$errorClass    = 'wpfs-form-control--error';
		$errorSelector = ".{$class}";

		return self::descriptor( self::FIELD_TYPE_INPUT, $name, $class, $selector, $errorClass, $errorSelector );
	}

	/**
	 * @param $type
	 * @param $name
	 * @param $class
	 * @param $selector
	 * @param $errorClass
	 * @param $errorSelector
	 * @param bool $hidden
	 *
	 * @return array
	 */
	public static final function descriptor( $type, $name, $class, $selector, $errorClass, $errorSelector, $hidden = false ) {
		return array(
			'type'          => $type,
			'name'          => $name,
			'class'         => $class,
			'selector'      => $selector,
			'errorClass'    => $errorClass,
			'errorSelector' => $errorSelector,
			'hidden'        => $hidden
		);
	}

	public static final function input( $name ) {
		$class         = 'wpfs-form-control';
		$selector      = ".{$class}[name='{$name}']";
		$errorClass    = 'wpfs-form-control--error';
		$errorSelector = ".{$class}";

		return self::descriptor( self::FIELD_TYPE_INPUT, $name, $class, $selector, $errorClass, $errorSelector );
	}

	public static final function inputHidden( $name ) {
		return self::descriptor( self::FIELD_TYPE_INPUT, $name, null, null, null, null, true );
	}

	public static final function selectMenu( $name ) {
		$class         = 'wpfs-form-control';
		$selector      = "select[name='{$name}']";
		$errorClass    = 'wpfs-form-control--error';
		$errorSelector = '#{fieldId}-button,#{fieldId}-menu';

		return self::descriptor( self::FIELD_TYPE_DROPDOWN, $name, $class, $selector, $errorClass, $errorSelector );
	}

	public static final function checkbox( $name ) {
		$class         = 'wpfs-form-check-input';
		$selector      = ".{$class}[name='{$name}']";
		$errorClass    = 'wpfs-form-check-input--error';
		$errorSelector = ".{$class}";

		return self::descriptor( self::FIELD_TYPE_CHECKBOX, $name, $class, $selector, $errorClass, $errorSelector );
	}

	public static final function captcha( $name ) {
		$class         = 'wpfs-form-captcha';
		$selector      = ".{$class}[data-wpfs-field-name='{$name}']";
		$errorClass    = 'wpfs-form-control--error';
		$errorSelector = ".{$class}";

		return self::descriptor( self::FIELD_TYPE_CAPTCHA, $name, $class, $selector, $errorClass, $errorSelector );
	}

	public static final function inputGroup( $name ) {
		$class         = 'wpfs-input-group-form-control';
		$selector      = ".{$class}[name='{$name}']";
		$errorClass    = 'wpfs-input-group--error';
		$errorSelector = '.' . 'wpfs-input-group';

		return self::descriptor( self::FIELD_TYPE_INPUT_GROUP, $name, $class, $selector, $errorClass, $errorSelector );
	}

	public static final function cardInput( $name ) {
		$class         = 'wpfs-form-card';
		$selector      = "input[name='{$name}']";
		$errorClass    = 'wpfs-form-control--error';
		$errorSelector = ".{$class}";

		return self::descriptor( self::FIELD_TYPE_CARD, $name, $class, $selector, $errorClass, $errorSelector );
	}

	public static final function stepper( $name ) {
		$class         = 'wpfs-stepper';
		$selector      = "input[name='{$name}']";
		$errorClass    = 'wpfs-form-control--error';
		$errorSelector = ".{$class}";

		return self::descriptor( self::FIELD_TYPE_CARD, $name, $class, $selector, $errorClass, $errorSelector );
	}

	/**
	 * @param $formHash
	 * @param $name
	 * @param $placeholder
	 * @param $caption
	 * @param $label
	 * @param null $index
	 *
	 * @return MM_WPFS_Control
	 */
	public static final function createControl( $formHash, $name, $placeholder, $caption, $label, $index ) {
		$control = new MM_WPFS_Control(
			MM_WPFS_Utils::generate_form_element_id( $name, $formHash, $index ),
			$name,
			$placeholder
		);
		$control->setCaption( $caption );
		$control->setLabel( $label );
		$control->setIndex( $index );

		return $control;
	}

}

/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2018.07.26.
 * Time: 14:38
 */
class MM_WPFS_Control {

	const ESCAPE_TYPE_NONE = 'none';
	const ESCAPE_TYPE_ATTRIBUTE = 'attribute';
	const ESCAPE_TYPE_HTML = 'html';
	const PHP_MULTI_VALUE_POST_PARAMETER_NAME_POSTFIX = '[]';

	protected $id;
	protected $name;
	protected $placeholder;
	protected $index;
	protected $caption;
	protected $value;
	protected $tooltip;
	protected $label;
	protected $labelEscape = self::ESCAPE_TYPE_HTML;
	protected $labelAttributes = array();
	protected $options = array();
	protected $attributes = array();
	/** @var  boolean $multiValue */
	protected $multiValue = false;

	/**
	 * MM_WPFS_ViewBlock constructor.
	 *
	 * @param $id
	 * @param $name
	 * @param $placeholder
	 */
	public function __construct( $id, $name, $placeholder ) {
		$this->id          = $id;
		$this->name        = $name;
		$this->placeholder = $placeholder;
	}

	/**
	 * @param mixed $id
	 */
	public function setId( $id ) {
		$this->id = $id;
	}

	/**
	 * @param mixed $name
	 */
	public function setName( $name ) {
		$this->name = $name;
	}

	/**
	 * @param mixed $placeholder
	 */
	public function setPlaceholder( $placeholder ) {
		$this->placeholder = $placeholder;
	}

	/**
	 * @param mixed $caption
	 */
	public function setCaption( $caption ) {
		$this->caption = $caption;
	}

	/**
	 * @param mixed $value
	 */
	public function setValue( $value ) {
		$this->value = $value;
	}

	/**
	 * @param mixed $tooltip
	 */
	public function setTooltip( $tooltip ) {
		$this->tooltip = $tooltip;
	}

	/**
	 * @param mixed $index
	 */
	public function setIndex( $index ) {
		$this->index = $index;
	}

	/**
	 * @param mixed $label
	 * @param string $labelEscape
	 */
	public function setLabel( $label, $labelEscape = self::ESCAPE_TYPE_HTML ) {
		$this->label = $label;
		if ( self::ESCAPE_TYPE_NONE === $labelEscape ) {
			$this->labelEscape = self::ESCAPE_TYPE_NONE;
		} else {
			$this->labelEscape = self::ESCAPE_TYPE_HTML;
		}
	}

	/**
	 * @param string $labelEscape
	 */
	public function setLabelEscape( $labelEscape ) {
		$this->labelEscape = $labelEscape;
	}

	/**
	 * @param array $labelAttributes
	 */
	public function setLabelAttributes( $labelAttributes ) {
		$this->labelAttributes = $labelAttributes;
	}

	/**
	 * @param array $options
	 */
	public function setOptions( $options ) {
		$this->options = $options;
	}

	/**
	 * @param array $attributes
	 */
	public function setAttributes( $attributes ) {
		$this->attributes = $attributes;
	}

	/**
	 * @param boolean $multiValue
	 */
	public function setMultiValue( $multiValue ) {
		$this->multiValue = $multiValue;
	}

	/**
	 * @param bool $echo
	 *
	 * @return bool|null|string|void
	 */
	public function name( $echo = true ) {
		$name = $this->name;
		if ( $this->multiValue ) {
			$name .= self::PHP_MULTI_VALUE_POST_PARAMETER_NAME_POSTFIX;
		}

		return $this->echoOrReturnValue( $echo, $name );
	}

	/**
	 * @param $echo
	 * @param $fieldValue
	 * @param string $escapeType
	 *
	 * @return bool|null|string|void
	 */
	protected function echoOrReturnValue( $echo, $fieldValue, $escapeType = self::ESCAPE_TYPE_ATTRIBUTE ) {
		$escapedValue = null;
		if ( self::ESCAPE_TYPE_ATTRIBUTE === $escapeType ) {
			$escapedValue = esc_attr( $fieldValue );
		} elseif ( self::ESCAPE_TYPE_HTML === $escapeType ) {
			$escapedValue = esc_html( $fieldValue );
		} else {
			$escapedValue = $fieldValue;
		}
		if ( $echo ) {
			echo( $escapedValue );

			return true;
		} else {
			return $escapedValue;
		}
	}

	/**
	 * @param bool $echo
	 *
	 * @return bool|null|string|void
	 */
	public function index( $echo = true ) {
		return $this->echoOrReturnValue( $echo, $this->index );
	}

	/**
	 * @param bool $echo
	 *
	 * @return bool|null|string|void
	 */
	public function id( $echo = true ) {
		return $this->echoOrReturnValue( $echo, $this->id );
	}

	/**
	 * @param bool $echo
	 *
	 * @return bool|null|string|void
	 */
	public function placeholder( $echo = true ) {
		return $this->echoOrReturnValue( $echo, $this->placeholder );
	}

	/**
	 * @param bool $echo
	 *
	 * @return bool|null|string|void
	 */
	public function caption( $echo = true ) {
		return $this->echoOrReturnValue( $echo, $this->caption, self::ESCAPE_TYPE_HTML );
	}

	/**
	 * @param bool $echo
	 *
	 * @return bool|null|string|void
	 */
	public function value( $echo = true ) {
		return $this->echoOrReturnValue( $echo, $this->value );
	}

	/**
	 * @param bool $echo
	 *
	 * @return mixed
	 */
	public function tooltip( $echo = true ) {
		return $this->echoOrReturnValue( $echo, $this->tooltip );
	}

	/**
	 * @param bool $echo
	 *
	 * @return bool|null|string|void
	 */
	public function label( $echo = true ) {
		return $this->echoOrReturnValue( $echo, $this->label, $this->labelEscape );
	}

	/**
	 * @param bool $echo
	 *
	 * @return array
	 */
	public function labelAttributes( $echo = true ) {
		if ( $echo ) {

			$labelAttributesAsString = $this->attributesAsString( $this->labelAttributes );

			echo( $labelAttributesAsString );
		}

		return $this->labelAttributes;
	}

	/**
	 * @param array $attributes
	 *
	 * @return string
	 */
	public static function attributesAsString( array $attributes ) {
		$attributesAsString = implode(
			' ',
			array_map(
				function ( $value, $key ) {
					return $key . '=' . '"' . esc_attr( $value ) . '"';
				},
				$attributes,
				array_keys( $attributes )
			)
		);

		return $attributesAsString;
	}

	/**
	 * @return array
	 */
	public function options() {
		return $this->options;
	}

	/**
	 * @param bool $echo
	 *
	 * @return array
	 */
	public function attributes( $echo = true ) {
		if ( $echo ) {

			$attributesAsString = $this->attributesAsString( $this->attributes );

			echo( $attributesAsString );
		}

		return $this->attributes;
	}
}

class MM_WPFS_FieldDescriptor {

	/**
	 * @var string
	 */
	protected $type;
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var string
	 */
	protected $class;
	/**
	 * @var string
	 */
	protected $selector;
	/**
	 * @var string
	 */
	protected $errorClass;
	/**
	 * @var string
	 */
	protected $errorSelector;

	/**
	 * MM_WPFS_FieldDescriptor constructor.
	 *
	 * @param string $type
	 * @param string $name
	 * @param string $class
	 * @param string $selector
	 * @param string $errorClass
	 * @param string $errorSelector
	 */
	public function __construct( $type, $name, $class, $selector, $errorClass, $errorSelector ) {
		$this->type          = $type;
		$this->name          = $name;
		$this->class         = $class;
		$this->selector      = $selector;
		$this->errorClass    = $errorClass;
		$this->errorSelector = $errorSelector;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getClass() {
		return $this->class;
	}

	/**
	 * @return string
	 */
	public function getSelector() {
		return $this->selector;
	}

	/**
	 * @return string
	 */
	public function getErrorClass() {
		return $this->errorClass;
	}

	/**
	 * @return string
	 */
	public function getErrorSelector() {
		return $this->errorSelector;
	}

}

abstract class MM_WPFS_FormView implements MM_WPFS_FormViewConstants {

	/** @var MM_WPFS_Control */
	protected $action;
	/** @var MM_WPFS_Control */
	protected $formName;
	/** @var MM_WPFS_Control */
	protected $formGetParameters;
	/** @var MM_WPFS_Control */
	protected $sameBillingAndShippingAddress;
	/** @var MM_WPFS_Control */
	protected $showBillingAddressPanelRadio;
	/** @var MM_WPFS_Control */
	protected $showShippingAddressPanelRadio;
	/** @var MM_WPFS_Control */
	protected $addressSwitcher;
	/** @var MM_WPFS_Control */
	protected $billingAddressPanel;
	/** @var MM_WPFS_Control */
	protected $billingName;
	/** @var MM_WPFS_Control */
	protected $billingAddressLine1;
	/** @var MM_WPFS_Control */
	protected $billingAddressLine2;
	/** @var MM_WPFS_Control */
	protected $billingAddressCity;
	/** @var MM_WPFS_Control */
	protected $billingAddressState;
	/** @var MM_WPFS_Control */
	protected $billingAddressZip;
	/** @var MM_WPFS_Control */
	protected $billingAddressCountry;
	/** @var MM_WPFS_Control */
	protected $shippingAddressPanel;
	/** @var MM_WPFS_Control */
	protected $shippingName;
	/** @var MM_WPFS_Control */
	protected $shippingAddressLine1;
	/** @var MM_WPFS_Control */
	protected $shippingAddressLine2;
	/** @var MM_WPFS_Control */
	protected $shippingAddressCity;
	/** @var MM_WPFS_Control */
	protected $shippingAddressState;
	/** @var MM_WPFS_Control */
	protected $shippingAddressZip;
	/** @var MM_WPFS_Control */
	protected $shippingAddressCountry;
	/** @var array */
	protected $customInputs;
	/** @var MM_WPFS_Control */
	protected $termsOfUseAccepted;
	/** @var MM_WPFS_Control */
	protected $submitButton;

	protected $form;
	protected $formHash;
	protected $attributes = array();

	/**
	 * MM_WPFS_FormView constructor.
	 *
	 * @param $form
	 */
	public function __construct( $form ) {

		$this->form                          = $form;
		$this->formHash                      = $this->getFormHash();
		$this->attributes                    = $this->getFormAttributes();
		$this->action                        = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_ACTION, null, null, null, null );
		$this->formName                      = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_FORM_NAME, null, null, null, null );
		$this->formGetParameters             = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_FORM_GET_PARAMETERS, null, null, null, null );
		$this->sameBillingAndShippingAddress = null;
		$this->showBillingAddressPanelRadio  = null;
		$this->showShippingAddressPanelRadio = null;
		$this->addressSwitcher               = null;
		$this->billingAddressPanel           = null;
		$this->billingName                   = null;
		$this->billingAddressLine1           = null;
		$this->billingAddressLine2           = null;
		$this->billingAddressZip             = null;
		$this->billingAddressState           = null;
		$this->billingAddressCity            = null;
		$this->billingAddressCountry         = null;
		$this->shippingAddressPanel          = null;
		$this->shippingName                  = null;
		$this->shippingAddressLine1          = null;
		$this->shippingAddressLine2          = null;
		$this->shippingAddressZip            = null;
		$this->shippingAddressState          = null;
		$this->shippingAddressCity           = null;
		$this->shippingAddressCountry        = null;
		$this->customInputs                  = array();
		$this->termsOfUseAccepted            = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_TERMS_OF_USE_ACCEPTED, null, null, $this->form->termsOfUseLabel, null );
		$this->submitButton                  = MM_WPFS_ControlUtils::createControl( $this->formHash, MM_WPFS_FormViewConstants::BUTTON_SUBMIT, null, $this->getSubmitButtonCaptionForForm( $form ), null, null );

		$this->action->setAttributes(
			array(
				'type' => 'hidden'
			)
		);
		$this->formName->setAttributes(
			array(
				'type' => 'hidden'
			)
		);
		$this->formName->setValue( $this->form->name );
		$this->formGetParameters->setAttributes(
			array(
				'type' => 'hidden'
			)
		);
		$this->termsOfUseAccepted->setLabelEscape( MM_WPFS_Control::ESCAPE_TYPE_NONE );

		$this->prepareCustomInputs();
		$this->prepareAddresses();

	}

	public function getFormHash() {
		$formType = MM_WPFS_Utils::getFormType( $this->form );
		$formId   = MM_WPFS_Utils::getFormId( $this->form );

		return esc_attr( MM_WPFS_Utils::generate_form_hash(
			$formType,
			$formId,
			$this->form->name )
		);
	}

	/**
	 * @return array
	 */
	protected function getFormAttributes() {
		$attributes = array();

		$attributes[ self::ATTR_ACTION ]              = '';
		$attributes[ self::ATTR_METHOD ]              = self::ATTR_METHOD_VALUE_POST;
		$attributes[ self::ATTR_CLASS ]               = self::ATTR_CLASS_VALUE_WPFS_FORM_WPFS_W_60;
		$attributes[ self::ATTR_ID ]                  = MM_WPFS_Utils::generateCSSFormID( $this->formHash );
		$attributes[ self::ATTR_DATA_WPFS_FORM_ID ]   = $this->form->name;
		$attributes[ self::ATTR_DATA_WPFS_FORM_HASH ] = $this->formHash;
		if ( 1 == $this->form->showCustomInput && $this->form->customInputs ) {
			$attributes[ self::ATTR_DATA_WPFS_CUSTOM_INPUT_TITLE ]    = $this->form->customInputTitle;
			$attributes[ self::ATTR_DATA_WPFS_CUSTOM_INPUTS ]         = $this->form->customInputs;
			$attributes[ self::ATTR_DATA_WPFS_CUSTOM_INPUT_REQUIRED ] = $this->form->customInputRequired;
		}
		if ( isset( $this->form->showAddress ) && 1 == $this->form->showAddress ) {
			$attributes[ self::ATTR_DATA_WPFS_SHOW_ADDRESS ] = $this->form->showAddress;
		}
		if ( isset( $this->form->showBillingAddress ) && 1 == $this->form->showBillingAddress ) {
			$attributes[ self::ATTR_DATA_WPFS_SHOW_BILLING_ADDRESS ] = $this->form->showBillingAddress;
		}
		if ( isset( $this->form->showShippingAddress ) && 1 == $this->form->showShippingAddress ) {
			$attributes[ self::ATTR_DATA_WPFS_SHOW_SHIPPING_ADDRESS ] = $this->form->showShippingAddress;
		}
		$attributes[ self::ATTR_DATA_WPFS_SHOW_TERMS_OF_USE ]                          = $this->form->showTermsOfUse;
		$attributes[ self::ATTR_DATA_WPFS_TERMS_OF_USE_NOT_CHECKED_ERROR_MESSAGE ]     = $this->form->termsOfUseNotCheckedErrorMessage;
		$attributes[ self::ATTR_DATA_WPFS_DECIMAL_SEPARATOR ]                          = $this->form->decimalSeparator;
		$attributes[ self::ATTR_DATA_WPFS_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE ]       = $this->form->showCurrencySymbolInsteadOfCode;
		$attributes[ self::ATTR_DATA_WPFS_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION ]       = $this->form->showCurrencySignAtFirstPosition;
		$attributes[ self::ATTR_DATA_WPFS_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT ] = $this->form->putWhitespaceBetweenCurrencyAndAmount;

		return $attributes;
	}

	/**
	 * @param $form
	 *
	 * @return string
	 */
	protected function getSubmitButtonCaptionForForm( $form ) {
		$submitButtonCaption = null;
		if ( $this instanceof MM_WPFS_PopupPaymentFormView || $this instanceof MM_WPFS_PopupSubscriptionFormView ||
             $this instanceof MM_WPFS_PopupCardCaptureFormView || $this instanceof MM_WPFS_PopupDonationFormView ) {
			$submitButtonCaption = MM_WPFS_Localization::translateLabel( $this->form->openButtonTitle );
		} elseif ( $this instanceof MM_WPFS_InlinePaymentFormView || $this instanceof MM_WPFS_InlineSubscriptionFormView ||
                   $this instanceof MM_WPFS_InlineCardCaptureFormView  || $this instanceof MM_WPFS_InlineDonationFormView ) {
			$submitButtonCaption = MM_WPFS_Localization::translateLabel( $form->buttonTitle );
		}

		if ( isset( $form->customAmount ) ) {
			if ( MM_WPFS::PAYMENT_TYPE_SPECIFIED_AMOUNT == $form->customAmount ) {
				if ( isset( $form->showButtonAmount ) && 1 == $form->showButtonAmount ) {
					$amountLabel         = MM_WPFS_Currencies::formatAndEscapeByForm( $form, $form->currency, $form->amount, false );
					$submitButtonCaption = $this->insertAmountIntoLabel( $submitButtonCaption, $amountLabel );
				}
			} elseif ( MM_WPFS::PAYMENT_TYPE_LIST_OF_AMOUNTS == $form->customAmount ) {
				if ( isset( $form->showButtonAmount ) && 1 == $form->showButtonAmount ) {
					$listOfAmounts = json_decode( $form->listOfAmounts );
					if ( JSON_ERROR_NONE === json_last_error() ) {
						$firstAmountElement  = reset( $listOfAmounts );
						$amountLabel         = MM_WPFS_Currencies::formatAndEscapeByForm( $form, $form->currency, $firstAmountElement[0], false );
						$submitButtonCaption = $this->insertAmountIntoLabel( $submitButtonCaption, $amountLabel );
					}
				}
			} elseif ( MM_WPFS::PAYMENT_TYPE_CUSTOM_AMOUNT == $form->customAmount ) {
				$submitButtonCaption = $this->insertAmountIntoLabel( $submitButtonCaption, '' );
			}
		}

		return $submitButtonCaption;
	}

	protected function containsAmountMacro( $label ) {
        return false === strpos( $label, MM_WPFS_FormViewConstants::MACRO_SUBMIT_BUTTON_CAPTION_AMOUNT ) ? false: true;
	}

	protected function insertAmountIntoLabel( $label, $amountLabel ) {
		if ( is_null( $label ) ) {
			return $label;
		}
		$labelWithAmount = $label;
		if ( false !== strpos( $labelWithAmount, MM_WPFS_FormViewConstants::MACRO_SUBMIT_BUTTON_CAPTION_AMOUNT ) ) {
			$labelWithAmount = trim( str_replace( MM_WPFS_FormViewConstants::MACRO_SUBMIT_BUTTON_CAPTION_AMOUNT, $amountLabel, $labelWithAmount ) );
		} else {
			$labelWithAmount .= ' ' . $amountLabel;
		}

		return $labelWithAmount;
	}

	protected function prepareCustomInputs() {
		if ( 1 == $this->form->showCustomInput ) {
			// tnagy legacy forms have only one custom input field
			if ( is_null( $this->form->customInputs ) ) {
				$input = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_CUSTOM_INPUT, null, null, MM_WPFS_Localization::translateLabel( $this->form->customInputTitle ), null );
				array_push( $this->customInputs, $input );
			} else {
				$customInputLabels = MM_WPFS_Utils::decode_custom_input_labels( $this->form->customInputs );
				foreach ( $customInputLabels as $index => $label ) {
					$customInputLabel = MM_WPFS_Localization::translateLabel( $label );
					$input            = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_CUSTOM_INPUT, null, null, $customInputLabel, $index );
					$input->setMultiValue( true );
					$input->setAttributes( array(
						self::ATTR_DATA_WPFS_CUSTOM_INPUT_FIELD => 'input',
						self::ATTR_DATA_WPFS_CUSTOM_INPUT_LABEL => $customInputLabel
					) );
					array_push( $this->customInputs, $input );
				}
			}
		}
	}

	protected function prepareAddresses() {
		if ( $this->showAddressSwitcher() ) {
			$this->sameBillingAndShippingAddress = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_SAME_BILLING_AND_SHIPPING_ADDRESS, null, null,
				/* translators: Label for a checkbox which controls whether billing and shipping address can be entered separately  */
				__( 'Same billing and shipping address', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$this->addressSwitcher               = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_ADDRESS_SWITCHER, null, null, null, null );
			$this->showBillingAddressPanelRadio  = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_ADDRESS_SWITCHER, null, null,
				/* translators: Form field label for billing address */
				__( 'Billing address', MM_WPFS::L10N_DOMAIN_PUBLIC ), 0 );
			$this->showShippingAddressPanelRadio = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_ADDRESS_SWITCHER, null, null,
				/* translators: Form field label for shipping address */
				__( 'Shipping address', MM_WPFS::L10N_DOMAIN_PUBLIC ), 1 );
		}

		$countryOptions = array();
		if ( $this->showAnyAddress() ) {
			$selectCountryOption = MM_WPFS_ControlUtils::createControl( $this->formHash, null, null,
				/* translators: Option label for the default option of the country selector dropdown */
				__( 'Select country', MM_WPFS::L10N_DOMAIN_PUBLIC ), null, null );
			$selectCountryOption->setAttributes(
				array(
					'disabled' => 'disabled'
				)
			);
			array_push( $countryOptions, $selectCountryOption );
			$defaultBillingCountrySelected = false;
			foreach ( MM_WPFS_Countries::get_available_countries() as $countryKey => $country ) {
				$option = MM_WPFS_ControlUtils::createControl( $this->formHash, null, null, MM_WPFS_Localization::translateLabel( $country['name'] ), null, null );
				if ( isset( $this->form->defaultBillingCountry ) && $this->form->defaultBillingCountry === $country['alpha-2'] ) {
					$defaultBillingCountrySelected = true;
					$option->setAttributes( array( 'selected' => 'selected' ) );
				}
				$option->setValue( $countryKey );
				array_push( $countryOptions, $option );
			}
			if ( false === $defaultBillingCountrySelected ) {
				$selectCountryOption->setAttributes(
					array(
						'disabled' => 'disabled',
						'selected' => 'selected'
					)
				);
			}

		}
		if ( isset( $this->form->showAddress ) && 1 == $this->form->showAddress ) {
			$this->billingAddressPanel   = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_BILLING_ADDRESS_PANEL, null, null, null, null );
			$this->billingName           = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_BILLING_NAME, null, null,
				/* translators: Form field label for the billing name */
				__( 'Billing name', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$this->billingAddressLine1   = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_BILLING_ADDRESS_LINE1, null, null,
				/* translators: Form field label for the billing address */
				__( 'Billing address street', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$this->billingAddressLine2   = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_BILLING_ADDRESS_LINE2, null, null,
				/* translators: Form field label for the billing address line 2 */
				__( 'Billing address line 2', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$this->billingAddressZip     = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_BILLING_ADDRESS_ZIP, null, null,
				/* translators: Form field label for the postal code of the billing address */
				__( 'Postal code', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$this->billingAddressState   = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_BILLING_ADDRESS_STATE, null, null,
				/* translators: Form field label for the state of the billing address */
				__( 'State', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$this->billingAddressCity    = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_BILLING_ADDRESS_CITY, null, null,
				/* translators: Form field label for the city of the billing address */
				__( 'City', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$this->billingAddressCountry = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_BILLING_ADDRESS_COUNTRY, null, null,
				/* translators: Form field label for the country of the billing address */
				__( 'Country', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$this->billingAddressCountry->setOptions( $countryOptions );
			if ( isset( $this->form->vatRateType ) ) {
				$this->billingAddressCountry->setAttributes( array( self::ATTR_DATA_WPFS_VAT_RATE_TYPE => $this->form->vatRateType ) );
			}
		}
		if ( isset( $this->form->showShippingAddress ) && 1 == $this->form->showShippingAddress ) {
			$this->shippingAddressPanel   = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_SHIPPING_ADDRESS_PANEL, null, null, null, null );
			$this->shippingName           = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_SHIPPING_NAME, null, null,
				/* translators: Form field label for the shipping name */
				__( 'Shipping name', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$this->shippingAddressLine1   = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_SHIPPING_ADDRESS_LINE1, null, null,
				/* translators: Form field label for the shipping address */
				__( 'Shipping address street', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$this->shippingAddressLine2   = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_SHIPPING_ADDRESS_LINE2, null, null,
				/* translators: Form field label for the shipping address line 2 */
				__( 'Shipping address line 2', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$this->shippingAddressZip     = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_SHIPPING_ADDRESS_ZIP, null, null,
				/* translators: Form field label for the postal code of the shipping address */
				__( 'Postal code', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$this->shippingAddressState   = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_SHIPPING_ADDRESS_STATE, null, null,
				/* translators: Form field label for the state of the shipping address */
				__( 'State', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$this->shippingAddressCity    = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_SHIPPING_ADDRESS_CITY, null, null,
				/* translators: Form field label for the city of the shipping address */
				__( 'City', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$this->shippingAddressCountry = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_SHIPPING_ADDRESS_COUNTRY, null, null,
				/* translators: Form field label for the country of the shipping address */
				__( 'Country', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$this->shippingAddressCountry->setOptions( $countryOptions );
		}
	}

	/**
	 * @return bool
	 */
	protected function showAddressSwitcher() {
		$showBillingAddress  = false;
		$showShippingAddress = false;
		if ( isset( $this->form->showAddress ) && 1 == $this->form->showAddress ) {
			$showBillingAddress = true;
		} elseif ( isset( $this->form->showBillingAddress ) && 1 == $this->form->showBillingAddress ) {
			$showBillingAddress = true;
		}
		if ( isset( $this->form->showShippingAddress ) && 1 == $this->form->showShippingAddress ) {
			$showShippingAddress = true;
		}

		return $showBillingAddress && $showShippingAddress;
	}

	/**
	 * @return bool
	 */
	protected function showAnyAddress() {
		$showAnyAddress = false;
		if ( isset( $this->form->showAddress ) && 1 == $this->form->showAddress ) {
			$showAnyAddress = true;
		}
		if (
			false === $showAnyAddress
			&& ( isset( $this->form->showBillingAddress ) && 1 == $this->form->showBillingAddress )
		) {
			$showAnyAddress = true;
		}
		if (
			false === $showAnyAddress
			&& ( isset( $this->form->showShippingAddress ) && 1 == $this->form->showShippingAddress )
		) {
			$showAnyAddress = true;
		}

		return $showAnyAddress;
	}

	/**
	 * Note: billing and shipping address fields must be added in child classes manually.
	 *
	 * @return array
	 */
	public static function getFields() {
		$fields = array(
			self::FIELD_ACTION                    => MM_WPFS_ControlUtils::inputHidden( self::FIELD_ACTION ),
			self::FIELD_FORM_NAME                 => MM_WPFS_ControlUtils::inputHidden( self::FIELD_FORM_NAME ),
			self::FIELD_CUSTOM_INPUT              => MM_WPFS_ControlUtils::customInput( self::FIELD_CUSTOM_INPUT ),
			self::FIELD_TERMS_OF_USE_ACCEPTED     => MM_WPFS_ControlUtils::checkbox( self::FIELD_TERMS_OF_USE_ACCEPTED ),
			self::FIELD_GOOGLE_RECAPTCHA_RESPONSE => MM_WPFS_ControlUtils::captcha( self:: FIELD_GOOGLE_RECAPTCHA_RESPONSE ),
			self::FIELD_NONCE                     => MM_WPFS_ControlUtils::inputHidden( self::FIELD_NONCE )
		);

		return $fields;
	}

	protected static function getPopupBillingAddressFields() {
		$fields = array(
			self::FIELD_BILLING_ADDRESS_LINE1   => MM_WPFS_ControlUtils::inputHidden( self::FIELD_BILLING_ADDRESS_LINE1 ),
			self::FIELD_BILLING_ADDRESS_LINE2   => MM_WPFS_ControlUtils::inputHidden( self::FIELD_BILLING_ADDRESS_LINE2 ),
			self::FIELD_BILLING_ADDRESS_ZIP     => MM_WPFS_ControlUtils::inputHidden( self::FIELD_BILLING_ADDRESS_ZIP ),
			self::FIELD_BILLING_ADDRESS_STATE   => MM_WPFS_ControlUtils::inputHidden( self::FIELD_BILLING_ADDRESS_STATE ),
			self::FIELD_BILLING_ADDRESS_CITY    => MM_WPFS_ControlUtils::inputHidden( self::FIELD_BILLING_ADDRESS_CITY ),
			self::FIELD_BILLING_ADDRESS_COUNTRY => MM_WPFS_ControlUtils::inputHidden( self::FIELD_BILLING_ADDRESS_COUNTRY ),
		);

		return $fields;
	}

	protected static function getInlineSameBillingAndShippingAddressField() {
		return array(
			self::FIELD_SAME_BILLING_AND_SHIPPING_ADDRESS => MM_WPFS_ControlUtils::checkbox( self::FIELD_SAME_BILLING_AND_SHIPPING_ADDRESS )
		);
	}

	protected static function getInlineBillingAddressFields() {
		$fields = array(
			self::FIELD_BILLING_NAME            => MM_WPFS_ControlUtils::input( self::FIELD_BILLING_NAME ),
			self::FIELD_BILLING_ADDRESS_LINE1   => MM_WPFS_ControlUtils::input( self::FIELD_BILLING_ADDRESS_LINE1 ),
			self::FIELD_BILLING_ADDRESS_LINE2   => MM_WPFS_ControlUtils::input( self::FIELD_BILLING_ADDRESS_LINE2 ),
			self::FIELD_BILLING_ADDRESS_ZIP     => MM_WPFS_ControlUtils::input( self::FIELD_BILLING_ADDRESS_ZIP ),
			self::FIELD_BILLING_ADDRESS_STATE   => MM_WPFS_ControlUtils::input( self::FIELD_BILLING_ADDRESS_STATE ),
			self::FIELD_BILLING_ADDRESS_CITY    => MM_WPFS_ControlUtils::input( self::FIELD_BILLING_ADDRESS_CITY ),
			self::FIELD_BILLING_ADDRESS_COUNTRY => MM_WPFS_ControlUtils::selectMenu( self::FIELD_BILLING_ADDRESS_COUNTRY ),
		);

		return $fields;
	}

	protected static function getInlineShippingAddressFields() {
		$fields = array(
			self::FIELD_SHIPPING_NAME            => MM_WPFS_ControlUtils::input( self::FIELD_SHIPPING_NAME ),
			self::FIELD_SHIPPING_ADDRESS_LINE1   => MM_WPFS_ControlUtils::input( self::FIELD_SHIPPING_ADDRESS_LINE1 ),
			self::FIELD_SHIPPING_ADDRESS_LINE2   => MM_WPFS_ControlUtils::input( self::FIELD_SHIPPING_ADDRESS_LINE2 ),
			self::FIELD_SHIPPING_ADDRESS_ZIP     => MM_WPFS_ControlUtils::input( self::FIELD_SHIPPING_ADDRESS_ZIP ),
			self::FIELD_SHIPPING_ADDRESS_STATE   => MM_WPFS_ControlUtils::input( self::FIELD_SHIPPING_ADDRESS_STATE ),
			self::FIELD_SHIPPING_ADDRESS_CITY    => MM_WPFS_ControlUtils::input( self::FIELD_SHIPPING_ADDRESS_CITY ),
			self::FIELD_SHIPPING_ADDRESS_COUNTRY => MM_WPFS_ControlUtils::selectMenu( self::FIELD_SHIPPING_ADDRESS_COUNTRY ),
		);

		return $fields;
	}

	/**
	 * @return mixed
	 */
	public function getFormName() {
		return ( isset( $this->form ) && isset( $this->form->name ) ) ? $this->form->name : null;
	}

	public function _formName() {
		echo( ( isset( $this->form ) && isset( $this->form->name ) ) ? esc_attr( $this->form->name ) : null );
	}

	/**
	 * @return array
	 */
	public function customInputs() {
		return $this->customInputs;
	}

	/**
	 * @return MM_WPFS_Control
	 */
	public function tOUAccepted() {
		return $this->termsOfUseAccepted;
	}

	/**
	 * @return MM_WPFS_Control
	 */
	public function submitButton() {
		return $this->submitButton;
	}

	/**
	 * @return MM_WPFS_Control
	 */
	public function sameBillingAndShippingAddress() {
		return $this->sameBillingAndShippingAddress;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function addressSwitcher() {
		return $this->addressSwitcher;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function billingAddressRadio() {
		return $this->showBillingAddressPanelRadio;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function shippingAddressRadio() {
		return $this->showShippingAddressPanelRadio;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function billingAddressPanel() {
		return $this->billingAddressPanel;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function billingName() {
		return $this->billingName;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function billingAddressLine1() {
		return $this->billingAddressLine1;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function billingAddressLine2() {
		return $this->billingAddressLine2;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function billingAddressCity() {
		return $this->billingAddressCity;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function billingAddressState() {
		return $this->billingAddressState;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function billingAddressZip() {
		return $this->billingAddressZip;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function billingAddressCountry() {
		return $this->billingAddressCountry;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function shippingAddressPanel() {
		return $this->shippingAddressPanel;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function shippingName() {
		return $this->shippingName;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function shippingAddressLine1() {
		return $this->shippingAddressLine1;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function shippingAddressLine2() {
		return $this->shippingAddressLine2;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function shippingAddressCity() {
		return $this->shippingAddressCity;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function shippingAddressState() {
		return $this->shippingAddressState;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function shippingAddressZip() {
		return $this->shippingAddressZip;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function shippingAddressCountry() {
		return $this->shippingAddressCountry;
	}

	public function formAttributes() {
		$attributesAsString = MM_WPFS_Control::attributesAsString( $this->attributes );

		echo( $attributesAsString );
	}

	/**
	 * @return MM_WPFS_Control
	 */
	public function action() {
		return $this->action;
	}

	/**
	 * @return MM_WPFS_Control
	 */
	public function formName() {
		return $this->formName;
	}

	/**
	 * @return MM_WPFS_Control
	 */
	public function formGetParameters() {
		return $this->formGetParameters;
	}

}

abstract class MM_WPFS_SubscriptionFormView extends MM_WPFS_FormView implements MM_WPFS_SubscriptionFormViewConstants {

	/** @var array $stripePlans */
	protected $stripePlans;
	/** @var array $selectedStripePlans */
	protected $selectedStripePlans;
	/** @var MM_WPFS_Control */
	protected $plans;
	/** @var MM_WPFS_Control */
	protected $firstPlan;
	/** @var MM_WPFS_Control */
	protected $coupon;
	/** @var MM_WPFS_Control */
	protected $planQuantity;

	protected $__currentVATPercent;

	/**
	 * MM_WPFS_SubscriptionFormView constructor.
	 *
	 * @param $form
	 * @param $stripePlans
	 */
	public function __construct( $form, $stripePlans ) {

		parent::__construct( $form );

		$this->stripePlans  = $stripePlans;
		$this->plans        = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_PLAN, null, null,
			/* translators: Form field label for the plan selector */
			__( 'Choose subscription plan', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
		$this->planQuantity = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_PLAN_QUANTITY, null, null,
			/* translators: Form field label for subscription quantity */
			__( 'Subscription quantity', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
		$this->coupon       = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_COUPON,
			/* translators: Form field placeholder for coupon code */
			__( 'Enter coupon code', MM_WPFS::L10N_DOMAIN_PUBLIC ), null,
			/* translators: Form field label for coupon code */
			__( 'Coupon', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
		$this->plans->setAttributes(
			array(
				self::ATTR_DATA_WPFS_FORM_ID => $this->form->name
			)
		);
		// todo tnagy change coupon tooltip later
		$this->coupon->setTooltip(
		/* translators: Tooltip label for the coupon code field */
			__( 'Apply a coupon for a discount to your subscription', MM_WPFS::L10N_DOMAIN_PUBLIC ) );

		$this->prepareCurrentVATPercent();
		$this->preparePlans();
		$this->prepareFirstPlan();
		$this->preparePlanQuantity();
	}

	protected function prepareCurrentVATPercent() {
		if ( MM_WPFS::VAT_RATE_TYPE_NO_VAT === $this->form->vatRateType ) {
			$this->__currentVATPercent = MM_WPFS::NO_VAT_PERCENT;
		} elseif ( MM_WPFS::VAT_RATE_TYPE_FIXED_VAT === $this->form->vatRateType ) {
			$this->__currentVATPercent = floatval( $this->form->vatPercent );
		} elseif ( MM_WPFS::VAT_RATE_TYPE_CUSTOM_VAT ) {
			$formType                  = MM_WPFS_Utils::getFormType( $this->form );
			$this->__currentVATPercent = floatval( apply_filters(
					MM_WPFS::FILTER_NAME_GET_VAT_PERCENT,
					MM_WPFS::NO_VAT_PERCENT,
					$this->form->defaultBillingCountry,
					$this->form->defaultBillingCountry,
					MM_WPFS_Utils::prepare_vat_filter_arguments(
						$this->formName(),
						$formType,
						null,
						array(
							'country' => $this->form->defaultBillingCountry
						),
						array()
					)
				)
			);
		} else {
			$this->__currentVATPercent = MM_WPFS::NO_VAT_PERCENT;
		}
	}

	protected function preparePlans() {
		if ( ! empty( $this->stripePlans ) ) {
			$planOptions               = array();
			$this->selectedStripePlans = MM_WPFS_Utils::get_sorted_form_plans( $this->stripePlans, $this->form->plans );
			foreach ( $this->selectedStripePlans as $index => $selectedStripePlan ) {
				$planCurrency          = $selectedStripePlan->currency;
				$planCancellationCount = MM_WPFS_Utils::get_cancellation_count_for_plan( $selectedStripePlan );
				$currencyArray         = MM_WPFS_Currencies::get_currency_for( $planCurrency );
				$planAmount            = $selectedStripePlan->amount;
				$planSetupFee          = MM_WPFS_Utils::get_setup_fee_for_plan( $selectedStripePlan );

                $controlLabel = MM_WPFS_Localization::translateLabel( $selectedStripePlan->product->name );
                $formattedAmount = MM_WPFS_Currencies::formatAndEscapeByForm( $this->form,
                                                                                $selectedStripePlan->currency,
                                                                                $selectedStripePlan->amount,
                                                                                true,
                                                                                true );
                $amountLabel  = MM_WPFS_Localization::getPriceAndIntervalLabel( $selectedStripePlan->interval, $selectedStripePlan->interval_count, $formattedAmount );

				$name         = null;
				if ( MM_WPFS::PLAN_SELECTOR_STYLE_LIST === $this->form->planSelectorStyle ) {
					$name = self::FIELD_PLAN;
				}
				$plan = MM_WPFS_ControlUtils::createControl( $this->formHash, $name, null, null, null, $index );
				$plan->setValue( $selectedStripePlan->id );
				$plan->setAttributes(
					array(
						self::ATTR_DATA_WPFS_VALUE                                      => $selectedStripePlan->id,
						self::ATTR_DATA_WPFS_PLAN_AMOUNT                                => MM_WPFS_Currencies::format_amount( $planCurrency, $planAmount, true ),
						self::ATTR_DATA_WPFS_PLAN_AMOUNT_IN_SMALLEST_COMMON_CURRENCY    => $planAmount,
						self::ATTR_DATA_WPFS_PLAN_SETUP_FEE                             => MM_WPFS_Currencies::format_amount( $planCurrency, $planSetupFee, true ),
						self::ATTR_DATA_WPFS_PLAN_SETUP_FEE_IN_SMALLEST_COMMON_CURRENCY => $planSetupFee,
						self::ATTR_DATA_WPFS_VAT_PERCENT                                => $this->__currentVATPercent,
						self::ATTR_DATA_WPFS_PLAN_NAME                                  => $controlLabel,
						self::ATTR_DATA_WPFS_INTERVAL                                   => $selectedStripePlan->interval,
						self::ATTR_DATA_WPFS_INTERVAL_COUNT                             => $selectedStripePlan->interval_count,
						self::ATTR_DATA_WPFS_CANCELLATION_COUNT                         => $planCancellationCount,
						self::ATTR_DATA_WPFS_CURRENCY                                   => $planCurrency,
						self::ATTR_DATA_WPFS_ZERO_DECIMAL_SUPPORT                       => $currencyArray['zeroDecimalSupport'] ? 'true' : 'false',
						self::ATTR_DATA_WPFS_CURRENCY_SYMBOL                            => MM_WPFS_Currencies::get_currency_symbol_for( $planCurrency )
					)
				);
				$plan->setLabel( sprintf( '%s - <strong>%s</strong>', $controlLabel, $amountLabel ), MM_WPFS_Control::ESCAPE_TYPE_NONE );
				array_push( $planOptions, $plan );
			}
			$this->plans->setOptions( $planOptions );

			if ( count( $this->plans->options() ) == 0 ) {
				$this->submitButton->setAttributes(
					array_merge(
						$this->submitButton->attributes( false ),
						array( 'disabled' => true )
					)
				);
			}
		}
	}

	protected function prepareFirstPlan() {
		$firstStripePlan = null;
		if ( ! empty( $this->stripePlans ) ) {
			if ( sizeof( $this->selectedStripePlans ) > 0 ) {
				$firstStripePlan = $this->selectedStripePlans[0];
			}
		}
		if ( ! is_null( $firstStripePlan ) ) {

			$planCurrency          = $firstStripePlan->currency;
			$planCancellationCount = MM_WPFS_Utils::get_cancellation_count_for_plan( $firstStripePlan );
			$currencyArray         = MM_WPFS_Currencies::get_currency_for( $planCurrency );
			$planAmount            = $firstStripePlan->amount;
			$planSetupFee          = MM_WPFS_Utils::get_setup_fee_for_plan( $firstStripePlan );
			$this->firstPlan       = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_PLAN, null, null, null, null );
			$controlLabel          = MM_WPFS_Localization::translateLabel( $firstStripePlan->product->name );
			$this->firstPlan->setValue( $firstStripePlan->id );
			$this->firstPlan->setAttributes(
				array(
					'type'                                                          => 'hidden',
					self::ATTR_DATA_WPFS_VALUE                                      => $firstStripePlan->id,
					self::ATTR_DATA_WPFS_PLAN_AMOUNT                                => MM_WPFS_Currencies::format_amount( $planCurrency, $planAmount, true ),
					self::ATTR_DATA_WPFS_PLAN_AMOUNT_IN_SMALLEST_COMMON_CURRENCY    => $planAmount,
					self::ATTR_DATA_WPFS_PLAN_SETUP_FEE                             => MM_WPFS_Currencies::format_amount( $planCurrency, $planSetupFee, true ),
					self::ATTR_DATA_WPFS_PLAN_SETUP_FEE_IN_SMALLEST_COMMON_CURRENCY => $planSetupFee,
					self::ATTR_DATA_WPFS_VAT_PERCENT                                => $this->__currentVATPercent,
					self::ATTR_DATA_WPFS_PLAN_NAME                                  => $controlLabel,
					self::ATTR_DATA_WPFS_INTERVAL                                   => $firstStripePlan->interval,
					self::ATTR_DATA_WPFS_INTERVAL_COUNT                             => $firstStripePlan->interval_count,
					self::ATTR_DATA_WPFS_CANCELLATION_COUNT                         => $planCancellationCount,
					self::ATTR_DATA_WPFS_CURRENCY                                   => $planCurrency,
					self::ATTR_DATA_WPFS_ZERO_DECIMAL_SUPPORT                       => $currencyArray['zeroDecimalSupport'] ? 'true' : 'false',
					self::ATTR_DATA_WPFS_CURRENCY_SYMBOL                            => MM_WPFS_Currencies::get_currency_symbol_for( $planCurrency )
				)
			);
		}
	}

	protected function preparePlanQuantity() {
		$attributes = array(
			self::ATTR_DATA_WPFS_STEPPER  => self::FIELD_PLAN_QUANTITY,
			self::ATTR_DATA_DEFAULT_VALUE => 1,
			self::ATTR_DATA_MINIMUM_VALUE => 1
		);
		if ( isset( $this->form->maximumQuantityOfSubscriptions ) && $this->form->maximumQuantityOfSubscriptions > 0 ) {
			$attributes[ self::ATTR_DATA_MAXIMUM_VALUE ] = $this->form->maximumQuantityOfSubscriptions;
		}
		$this->planQuantity->setAttributes( $attributes );
	}

	/**
	 * @return array
	 */
	public static function getFields() {

		$fields = array(
			self::FIELD_PLAN          => MM_WPFS_ControlUtils::selectMenu( self::FIELD_PLAN ),
			self::FIELD_PLAN_QUANTITY => MM_WPFS_ControlUtils::stepper( self::FIELD_PLAN_QUANTITY ),
			self::FIELD_COUPON        => MM_WPFS_ControlUtils::inputGroup( self::FIELD_COUPON )
		);

		return array_merge( $fields, parent::getFields() );
	}

	/**
	 * @return array
	 */
	public function getSelectedStripePlanIds() {
		$planList = array();

		foreach ( $this->selectedStripePlans as $index => $selectedStripePlan ) {
			$planList[ $selectedStripePlan->id ] = $selectedStripePlan->id;
		}

		return $planList;
	}

	public function firstPlan() {
		return $this->firstPlan;
	}

	/**
	 * @return mixed
	 */
	public function getCurrentVATPercent() {
		return $this->__currentVATPercent;
	}

	/**
	 * @return MM_WPFS_Control
	 */
	public function plans() {
		return $this->plans;
	}

	/**
	 * @return MM_WPFS_Control
	 */
	public function planQuantity() {
		return $this->planQuantity;
	}

	/**
	 * @return MM_WPFS_Control
	 */
	public function coupon() {
		return $this->coupon;
	}

}

abstract class MM_WPFS_PaymentFormView extends MM_WPFS_FormView implements MM_WPFS_PaymentFormViewConstants {

	const AMOUNT_PATTERN = '{amount}';

	protected $currencyCode;
	protected $currencySymbol;
	protected $currencyName;
	protected $currencyZeroDecimalSupport;
	/** @var MM_WPFS_Control */
	protected $customAmount;
	/** @var MM_WPFS_Control */
	protected $customAmountOptions;

	/**
	 * MM_WPFS_PaymentFormView constructor.
	 *
	 * @param $form
	 */
	public function __construct( $form ) {

		parent::__construct( $form );

		$this->currencyCode               = null;
		$this->currencyName               = null;
		$this->currencySymbol             = null;
		$this->currencyZeroDecimalSupport = null;
		$this->customAmount               = null;
		$this->customAmountOptions        = null;

		$this->prepareCurrency();
		$this->prepareCustomAmountAndOptions();

		$this->attributes = $this->getFormAttributes();

	}


	protected function prepareCurrency() {
		$currency                         = MM_WPFS_Currencies::get_currency_for( $this->form->currency );
		$this->currencyCode               = $currency['code'];
		$this->currencyName               = $currency['name'];
		$this->currencySymbol             = $currency['symbol'];
		$this->currencyZeroDecimalSupport = $currency['zeroDecimalSupport'];
	}

	protected function prepareCustomAmountAndOptions() {
		$amountButtonTitle = $this->form->buttonTitle;
		if ( $this instanceof MM_WPFS_PopupPaymentFormView || $this instanceof MM_WPFS_PopupCardCaptureFormView ) {
			$amountButtonTitle = $this->form->openButtonTitle;
		}
		$attributes = array(
			self::ATTR_DATA_WPFS_BUTTON_TITLE          => MM_WPFS_Localization::translateLabel( $amountButtonTitle ),
			self::ATTR_DATA_WPFS_SHOW_AMOUNT           => isset( $this->form->showButtonAmount ) ? $this->form->showButtonAmount : null,
			self::ATTR_DATA_WPFS_CURRENCY              => $this->currencyCode,
			self::ATTR_DATA_WPFS_ZERO_DECIMAL_SUPPORT  => $this->currencyZeroDecimalSupport ? 'true' : 'false',
			self::ATTR_DATA_WPFS_CURRENCY_SYMBOL       => $this->currencySymbol,
			self::ATTR_DATA_WPFS_FORM_ID               => $this->formHash,
			self::ATTR_DATA_WPFS_AMOUNT_SELECTOR_STYLE => $this->form->amountSelectorStyle
		);
		if ( MM_WPFS::PAYMENT_TYPE_CUSTOM_AMOUNT == $this->form->customAmount ) {
			$this->customAmount = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_CUSTOM_AMOUNT_UNIQUE, null, null,
				/* translators: Form field label for the payment amount that can be entered manually (think paying invoices) */
				__( 'Amount', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$this->customAmount->setAttributes( $attributes );
			$this->customAmount->setLabelAttributes( array(
					'class' => 'wpfs-form-label'
				)
			);
		} elseif ( MM_WPFS::PAYMENT_TYPE_LIST_OF_AMOUNTS == $this->form->customAmount ) {
			$this->customAmountOptions = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_CUSTOM_AMOUNT, null, null,
				/* translators: Form field label for selecting a one-time payment amount */
				__( 'Select amount', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$this->customAmountOptions->setAttributes( $attributes );
			$customAmountOptions = array();
			$listOfAmounts       = json_decode( $this->form->listOfAmounts );
			$lastIndex           = - 1;
			foreach ( $listOfAmounts as $index => $listElement ) {
				$lastIndex        = $index;
				$amount           = $listElement[0];
				$description      = $listElement[1];
				$amountLabel      = MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $this->currencyCode, $amount, false );
				$descriptionLabel = MM_WPFS_Localization::translateLabel( $description );
				if ( false !== strpos( $description, self::AMOUNT_PATTERN ) ) {
					$descriptionLabel = str_replace( self::AMOUNT_PATTERN, $amountLabel, $descriptionLabel );
				}
				$customAmountOption = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_CUSTOM_AMOUNT, null, null, $descriptionLabel, $index );
				$formattedAmount    = MM_WPFS_Currencies::format_amount( $this->currencyCode, $amount );
				$customAmountOption->setValue( $formattedAmount );
				$optionAttributes = array(
					self::ATTR_DATA_WPFS_AMOUNT_IN_SMALLEST_COMMON_CURRENCY => $amount,
					self::ATTR_DATA_WPFS_AMOUNT_INDEX                       => $index,
					self::ATTR_DATA_WPFS_AMOUNT_DESCRIPTION                 => $descriptionLabel
				);
				$optionAttributes = array_merge( $attributes, $optionAttributes );
				$customAmountOption->setAttributes( $optionAttributes );
				if ( MM_WPFS::AMOUNT_SELECTOR_STYLE_DROPDOWN === $this->form->amountSelectorStyle ) {
					$customAmountOption->setLabel( $descriptionLabel );
				} elseif ( MM_WPFS::AMOUNT_SELECTOR_STYLE_BUTTON_GROUP === $this->form->amountSelectorStyle ) {
					$customAmountOption->setLabel( $amountLabel );
				} elseif ( MM_WPFS::AMOUNT_SELECTOR_STYLE_RADIO_BUTTONS === $this->form->amountSelectorStyle ) {
					$controlLabel = MM_WPFS_Localization::translateLabel( $description );
					if ( false !== strpos( $description, self::AMOUNT_PATTERN ) ) {
						$controlLabel = str_replace( self::AMOUNT_PATTERN, $amountLabel, $controlLabel );
						$customAmountOption->setLabel( $controlLabel, MM_WPFS_Control::ESCAPE_TYPE_NONE );
					} else {
						$customAmountOption->setLabel( sprintf( '%s - <strong>%s</strong>', $controlLabel, $amountLabel ), MM_WPFS_Control::ESCAPE_TYPE_NONE );
					}
				} else {
					$customAmountOption->setLabel( $descriptionLabel );
				}
				array_push( $customAmountOptions, $customAmountOption );
			}
			if ( 1 == $this->form->allowListOfAmountsCustom ) {
				$this->customAmount     = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_CUSTOM_AMOUNT_UNIQUE, null, null,
					/* translators: Form field label for the payment amount that can be entered manually (think paying invoices) */
					__( 'Amount', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
				$customAmountAttributes = array_merge( $attributes, array( 'disabled' => true ) );
				$this->customAmount->setAttributes( $customAmountAttributes );
				$this->customAmount->setLabelAttributes( array(
						'class' => 'wpfs-sr-only'
					)
				);
				$customAmountOption = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_CUSTOM_AMOUNT, null, null,
					/* translators: Button label for entering a payment amount manually (think donation form) */
					__( 'Other', MM_WPFS::L10N_DOMAIN_PUBLIC ), $lastIndex + 1 );
				$customAmountOption->setValue( 'other' );
				$customAmountOption->setAttributes( $attributes );
				array_push( $customAmountOptions, $customAmountOption );
			}

			$this->customAmountOptions->setOptions( $customAmountOptions );
		}
	}

	/**
	 * @return array
	 */
	protected function getFormAttributes() {
		$attributes = array();

		$attributes[ self::ATTR_DATA_WPFS_AMOUNT_TYPE ] = $this->form->customAmount;
		if ( MM_WPFS::PAYMENT_TYPE_SPECIFIED_AMOUNT === $this->form->customAmount || MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE === $this->form->customAmount ) {
			$attributes[ self::ATTR_DATA_WPFS_AMOUNT ]               = $this->form->amount;
			$attributes[ self::ATTR_DATA_WPFS_CURRENCY ]             = $this->currencyCode;
			$attributes[ self::ATTR_DATA_WPFS_ZERO_DECIMAL_SUPPORT ] = $this->currencyZeroDecimalSupport ? 'true' : 'false';
			$attributes[ self::ATTR_DATA_WPFS_CURRENCY_SYMBOL ]      = $this->currencySymbol;
		} elseif ( MM_WPFS::PAYMENT_TYPE_LIST_OF_AMOUNTS === $this->form->customAmount ) {
			$attributes[ self::ATTR_DATA_WPFS_AMOUNT_SELECTOR_STYLE ]        = $this->form->amountSelectorStyle;
			$attributes[ self::ATTR_DATA_WPFS_ALLOW_LIST_OF_AMOUNTS_CUSTOM ] = $this->form->allowListOfAmountsCustom;
		}

		return array_merge( $attributes, parent::getFormAttributes() );
	}

	/**
	 * @return array
	 */
	public static function getFields() {
		$fields = array(
			self::FIELD_CUSTOM_AMOUNT_UNIQUE => MM_WPFS_ControlUtils::inputGroup( self::FIELD_CUSTOM_AMOUNT_UNIQUE )
		);

		return array_merge( $fields, parent::getFields() );
	}

	public function _currencySign() {
		if ( 1 == $this->form->showCurrencySymbolInsteadOfCode ) {
			$this->_currencySymbol();
		} else {
			$this->_currencyCode();
		}
	}

	public function _currencySymbol() {
		echo( esc_attr( $this->currencySymbol ) );
	}

	public function _currencyCode() {
		echo( esc_attr( $this->currencyCode ) );
	}

	public function showCurrencySignAtFirstPosition() {
		return 1 == $this->form->showCurrencySignAtFirstPosition;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function customAmount() {
		return $this->customAmount;
	}

	/**
	 * @return MM_WPFS_Control|null
	 */
	public function customAmountOptions() {
		return $this->customAmountOptions;
	}

}

class MM_WPFS_InlinePaymentFormView extends MM_WPFS_PaymentFormView {

	use MM_WPFS_InlineFormView;

	/**
	 * MM_WPFS_InlinePaymentFormView constructor.
	 *
	 * @param $form
	 */
	public function __construct( $form ) {
		parent::__construct( $form );
		$this->initInlineFields( $this->formHash );

		$this->action->setValue( self::FIELD_ACTION_VALUE_INLINE_PAYMENT_CHARGE );

	}

	/**
	 * @return array
	 */
	public static function getFields() {
		$inlineFields = self::getInlineFields();
		$inlineFields = array_merge( $inlineFields, self::getInlineSameBillingAndShippingAddressField() );
		$inlineFields = array_merge( $inlineFields, self::getInlineBillingAddressFields() );
		$inlineFields = array_merge( $inlineFields, self::getInlineShippingAddressFields() );

		return array_merge( $inlineFields, parent::getFields() );
	}

	/**
	 * @return array
	 */
	public function getFormAttributes() {
		$attributes = array();

		$attributes[ self::ATTR_DATA_WPFS_FORM_TYPE ] = MM_WPFS::FORM_TYPE_INLINE_PAYMENT;
		$attributes                                   = array_merge( $attributes, parent::getFormAttributes() );

		$inlineFormAttributes = $this->getInlineFormAttributes( $this->form );

		return array_merge( $attributes, $inlineFormAttributes );
	}

}

class MM_WPFS_InlineSubscriptionFormView extends MM_WPFS_SubscriptionFormView {

	use MM_WPFS_InlineFormView;

	/**
	 * MM_WPFS_InlineSubscriptionFormView constructor.
	 *
	 * @param $form
	 * @param $stripePlans
	 */
	public function __construct( $form, $stripePlans ) {
		parent::__construct( $form, $stripePlans );
		$this->initInlineFields( $this->formHash );

		$this->action->setValue( self::FIELD_ACTION_VALUE_INLINE_SUBSCRIPTION_CHARGE );

	}

	/**
	 * @return array
	 */
	public static function getFields() {
		$inlineFields = self::getInlineFields();
		$inlineFields = array_merge( $inlineFields, self::getInlineSameBillingAndShippingAddressField() );
		$inlineFields = array_merge( $inlineFields, self::getInlineBillingAddressFields() );
		$inlineFields = array_merge( $inlineFields, self::getInlineShippingAddressFields() );

		return array_merge( $inlineFields, parent::getFields() );
	}

	/**
	 * @return array
	 */
	public function getFormAttributes() {

		$attributes = array();

		$attributes[ self::ATTR_DATA_WPFS_FORM_TYPE ] = MM_WPFS::FORM_TYPE_INLINE_SUBSCRIPTION;
		$attributes                                   = array_merge( $attributes, parent::getFormAttributes() );

		$inlineFormAttributes = $this->getInlineFormAttributes( $this->form );

		return array_merge( $attributes, $inlineFormAttributes );
	}

}

abstract class MM_WPFS_DonationFormView extends MM_WPFS_FormView implements MM_WPFS_DonationFormViewConstants {

    protected $currencyCode;
    protected $currencySymbol;
    protected $currencyName;
    protected $currencyZeroDecimalSupport;

    protected $customAmount;
    /** @var MM_WPFS_Control */
    protected $donationAmountOptions;
    /** @var MM_WPFS_Control */
    protected $donationFrequencyOptions;

    /**
     * MM_WPFS_PaymentFormView constructor.
     *
     * @param $form
     */
    public function __construct( $form ) {

        parent::__construct( $form );

        $this->currencyCode               = null;
        $this->currencyName               = null;
        $this->currencySymbol             = null;
        $this->currencyZeroDecimalSupport = null;
        $this->customAmount               = null;
        $this->donationAmountOptions      = null;
        $this->donationFrequencyOptions   = null;

        $this->prepareCurrency();
        $this->prepareDonationAmountOptions();
        $this->prepareDonationFrequencyOptions();

        $this->attributes = $this->getFormAttributes();

    }

    protected function prepareCurrency() {
        $currency                         = MM_WPFS_Currencies::get_currency_for( $this->form->currency );
        $this->currencyCode               = $currency['code'];
        $this->currencyName               = $currency['name'];
        $this->currencySymbol             = $currency['symbol'];
        $this->currencyZeroDecimalSupport = $currency['zeroDecimalSupport'];
    }


    protected function getAmountButtonTitle() {
        $amountButtonTitle = $this->form->buttonTitle;
        if ( $this instanceof MM_WPFS_PopupDonationFormView ) {
            $amountButtonTitle = $this->form->openButtonTitle;
        }

        return $amountButtonTitle;
    }

    protected function getSubmitButtonCaptionForForm( $form ) {
        $caption = $this->getAmountButtonTitle();

        if ( $this->containsAmountMacro( $caption ) ) {
            $donationAmounts = MM_WPFS_Utils::decodeJsonArray( $this->form->donationAmounts );
            if ( count( $donationAmounts) > 0 ) {
                $donationAmount = (int)$donationAmounts[0];
                $amountLabel = MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $this->form->currency, $donationAmount, false );

                $caption = $this->insertAmountIntoLabel( $caption, $amountLabel );
            }
        }

        return $caption;
    }

    protected function createDonationFrequencyOption( $label, $value, $index ) {
        $frequencyOption = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_DONATION_FREQUENCY, null, null, $label, $index );
        $frequencyOption->setValue( $value );
        $frequencyAttributes = array();
        $frequencyOption->setAttributes( $frequencyAttributes );
        $frequencyOption->setLabel( $label );

        return $frequencyOption;
    }

    protected function prepareDonationFrequencyOptions() {
        $isRecurringDonation =  $this->form->allowDailyRecurring == 1 ||
                                $this->form->allowWeeklyRecurring == 1 ||
                                $this->form->allowMonthlyRecurring == 1 ||
                                $this->form->allowAnnualRecurring == 1;

        if ( $isRecurringDonation ) {
            $donationFrequencyAttributes = array();

            $this->donationFrequencyOptions = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_DONATION_FREQUENCY, null, null,
                /* translators: Form field label for selecting a one-time donation amount */
                __( 'Donation frequency', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
            $this->donationFrequencyOptions->setAttributes( $donationFrequencyAttributes );

            $donationFrequencyOptions = array();
            $frequencyOptionIndex = 0;

            // Let's add the "one-time" frequency
            $oneTimeOption = $this->createDonationFrequencyOption( __( 'One-time', MM_WPFS::L10N_DOMAIN_PUBLIC ),
                                                              self::FIELD_VALUE_DONATION_FREQUENCY_ONE_TIME, $frequencyOptionIndex );
            array_push( $donationFrequencyOptions,  $oneTimeOption);
            $frequencyOptionIndex += 1;

            // Let's add the frequencies selected by the the user
            if ( $this->form->allowDailyRecurring == 1 ) {
                $dailyOption = $this->createDonationFrequencyOption( __( 'Daily', MM_WPFS::L10N_DOMAIN_PUBLIC ),
                    self::FIELD_VALUE_DONATION_FREQUENCY_DAILY, $frequencyOptionIndex );
                array_push( $donationFrequencyOptions, $dailyOption );
                $frequencyOptionIndex += 1;
            }
            if ( $this->form->allowWeeklyRecurring == 1 ) {
                $weeklyOption = $this->createDonationFrequencyOption( __( 'Weekly', MM_WPFS::L10N_DOMAIN_PUBLIC ),
                    self::FIELD_VALUE_DONATION_FREQUENCY_WEEKLY, $frequencyOptionIndex );
                array_push( $donationFrequencyOptions, $weeklyOption );
                $frequencyOptionIndex += 1;
            }
            if ( $this->form->allowMonthlyRecurring == 1 ) {
                $monthlyOption = $this->createDonationFrequencyOption( __( 'Monthly', MM_WPFS::L10N_DOMAIN_PUBLIC ),
                    self::FIELD_VALUE_DONATION_FREQUENCY_MONTHLY, $frequencyOptionIndex );
                array_push( $donationFrequencyOptions, $monthlyOption );
                $frequencyOptionIndex += 1;
            }
            if ( $this->form->allowAnnualRecurring == 1 ) {
                $annualOption = $this->createDonationFrequencyOption( __( 'Annual', MM_WPFS::L10N_DOMAIN_PUBLIC ),
                    self::FIELD_VALUE_DONATION_FREQUENCY_ANNUAL, $frequencyOptionIndex );
                array_push( $donationFrequencyOptions, $annualOption );
            }

            $this->donationFrequencyOptions->setOptions( $donationFrequencyOptions );
        }
    }


    protected function prepareDonationAmountOptions() {
        $amountButtonTitle = $this->getAmountButtonTitle();

        $attributes = array(
            self::ATTR_DATA_WPFS_BUTTON_TITLE          => MM_WPFS_Localization::translateLabel( $amountButtonTitle ),
            self::ATTR_DATA_WPFS_CURRENCY              => $this->currencyCode,
            self::ATTR_DATA_WPFS_ZERO_DECIMAL_SUPPORT  => $this->currencyZeroDecimalSupport ? 'true' : 'false',
            self::ATTR_DATA_WPFS_CURRENCY_SYMBOL       => $this->currencySymbol,
            self::ATTR_DATA_WPFS_FORM_ID               => $this->formHash,
            self::ATTR_DATA_WPFS_SHOW_AMOUNT           => $this->containsAmountMacro( $amountButtonTitle ) ? 1 : 0,

        );

        $this->donationAmountOptions = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_CUSTOM_AMOUNT, null, null,
            /* translators: Form field label for selecting a one-time donation amount */
            __( 'Donation amount', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
        $this->donationAmountOptions->setAttributes( $attributes );

        $donationAmountOptions   = array();
        $donationAmountsArray  = MM_WPFS_Utils::decodeJsonArray( $this->form->donationAmounts );
        $lastIndex             = - 1;
        foreach ( $donationAmountsArray as $index => $listElement ) {
            $lastIndex        = $index;
            $donationAmount   = (int)$listElement;
            $amountLabel      = MM_WPFS_Currencies::formatAndEscapeByForm( $this->form, $this->currencyCode, $donationAmount, false );
            $customAmountOption = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_CUSTOM_AMOUNT, null, null, $amountLabel, $index );
            $formattedAmount    = MM_WPFS_Currencies::format_amount( $this->currencyCode, $donationAmount );
            $customAmountOption->setValue( $formattedAmount );
            $optionAttributes = array(
                self::ATTR_DATA_WPFS_AMOUNT_IN_SMALLEST_COMMON_CURRENCY => $donationAmount,
                self::ATTR_DATA_WPFS_AMOUNT_INDEX                       => $index,
            );
            $optionAttributes = array_merge( $attributes, $optionAttributes );
            $customAmountOption->setAttributes( $optionAttributes );
            $customAmountOption->setLabel( $amountLabel );
            array_push( $donationAmountOptions, $customAmountOption );
        }

        if ( 1 == $this->form->allowCustomDonationAmount ) {
            $this->customAmount     = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_CUSTOM_AMOUNT_UNIQUE, null, null,
                /* translators: Form field label for the donation amount that can be entered manually */
                __( 'Amount', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
            $customAmountAttributes = array_merge( $attributes, array( 'disabled' => true ) );
            $this->customAmount->setAttributes( $customAmountAttributes );
            $this->customAmount->setLabelAttributes( array(
                    'class' => 'wpfs-sr-only'
                )
            );
            $customAmountOption = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_CUSTOM_AMOUNT, null, null,
                /* translators: Button label for entering a payment amount manually (think donation form) */
                __( 'Other', MM_WPFS::L10N_DOMAIN_PUBLIC ), $lastIndex + 1 );
            $customAmountOption->setValue( 'other' );
            $customAmountOption->setAttributes( $attributes );
            array_push( $donationAmountOptions, $customAmountOption );
        }

        $this->donationAmountOptions->setOptions( $donationAmountOptions );
    }

    /**
     * @return array
     */
    protected function getFormAttributes() {
        $attributes = array();

        $attributes[ self::ATTR_DATA_WPFS_CURRENCY ]                     = $this->currencyCode;
        $attributes[ self::ATTR_DATA_WPFS_ZERO_DECIMAL_SUPPORT ]         = $this->currencyZeroDecimalSupport ? 'true' : 'false';
        $attributes[ self::ATTR_DATA_WPFS_CURRENCY_SYMBOL ]              = $this->currencySymbol;
        $attributes[ self::ATTR_DATA_WPFS_AMOUNT_TYPE ]                  = MM_WPFS::PAYMENT_TYPE_LIST_OF_AMOUNTS;
        $attributes[ self::ATTR_DATA_WPFS_ALLOW_LIST_OF_AMOUNTS_CUSTOM ] = $this->form->allowCustomDonationAmount;
        $attributes[ self::ATTR_DATA_WPFS_AMOUNT_SELECTOR_STYLE ]        = MM_WPFS::AMOUNT_SELECTOR_STYLE_BUTTON_GROUP;

        return array_merge( $attributes, parent::getFormAttributes() );
    }

    /**
     * @return array
     */
    public static function getFields() {
        $fields = array(
            self::FIELD_CUSTOM_AMOUNT_UNIQUE => MM_WPFS_ControlUtils::inputGroup( self::FIELD_CUSTOM_AMOUNT_UNIQUE )
        );

        return array_merge( $fields, parent::getFields() );
    }

    public function _currencySign() {
        if ( 1 == $this->form->showCurrencySymbolInsteadOfCode ) {
            $this->_currencySymbol();
        } else {
            $this->_currencyCode();
        }
    }

    public function _currencySymbol() {
        echo( esc_attr( $this->currencySymbol ) );
    }

    public function _currencyCode() {
        echo( esc_attr( $this->currencyCode ) );
    }

    public function showCurrencySignAtFirstPosition() {
        return 1 == $this->form->showCurrencySignAtFirstPosition;
    }

    /**
     * @return MM_WPFS_Control|null
     */
    public function customAmount() {
        return $this->customAmount;
    }

    /**
     * @return MM_WPFS_Control|null
     */
    public function donationAmountOptions() {
        return $this->donationAmountOptions;
    }

    /**
     * @return MM_WPFS_Control|null
     */
    public function donationFrequencyOptions() {
        return $this->donationFrequencyOptions;
    }

}

class MM_WPFS_InlineDonationFormView extends MM_WPFS_DonationFormView {

    use MM_WPFS_InlineFormView;

    /**
     * MM_WPFS_InlineDonationFormView constructor.
     *
     * @param $form
     */
    public function __construct( $form ) {
        parent::__construct( $form );
        $this->initInlineFields( $this->formHash );

        $this->action->setValue( self::FIELD_ACTION_VALUE_INLINE_DONATION_CHARGE );

    }

    /**
     * @return array
     */
    public static function getFields() {
        $inlineFields = self::getInlineFields();
        $inlineFields = array_merge( $inlineFields, self::getInlineSameBillingAndShippingAddressField() );
        $inlineFields = array_merge( $inlineFields, self::getInlineBillingAddressFields() );
        $inlineFields = array_merge( $inlineFields, self::getInlineShippingAddressFields() );

        return array_merge( $inlineFields, parent::getFields() );
    }

    /**
     * @return array
     */
    public function getFormAttributes() {
        $attributes = array();

        $attributes[ self::ATTR_DATA_WPFS_FORM_TYPE ] = MM_WPFS::FORM_TYPE_INLINE_DONATION;
        $attributes                                   = array_merge( $attributes, parent::getFormAttributes() );

        $inlineFormAttributes = $this->getInlineFormAttributes( $this->form );

        return array_merge( $attributes, $inlineFormAttributes );
    }

}

class MM_WPFS_PopupDonationFormView extends MM_WPFS_DonationFormView {

    use MM_WPFS_PopupFormView;

    /**
     * MM_WPFS_PopupDonationFormView constructor.
     *
     * @param $form
     */
    public function __construct( $form ) {
        parent::__construct( $form );

        $this->action->setValue( self::FIELD_ACTION_VALUE_POPUP_DONATION_CHARGE );
    }

    public static function getFields() {
        $fields = self::getPopupBillingAddressFields();

        return array_merge( $fields, parent::getFields() );
    }

    protected function getFormAttributes() {
        $attributes = array();

        $attributes[ self::ATTR_DATA_WPFS_FORM_TYPE ] = MM_WPFS::FORM_TYPE_POPUP_DONATION;
        $attributes                                   = array_merge( $attributes, parent::getFormAttributes() );

        $popupFormAttributes = $this->getPopupFormAttributes( $this->form );

        return array_merge( $attributes, $popupFormAttributes );
    }

}


class MM_WPFS_InlineCardCaptureFormView extends MM_WPFS_InlinePaymentFormView {

	use MM_WPFS_InlineFormView;

	/**
	 * MM_WPFS_InlineCardCaptureFormView constructor.
	 *
	 * @param $form
	 */
	public function __construct( $form ) {
		parent::__construct( $form );
	}

	public function getFormAttributes() {
		$attributes = parent::getFormAttributes();

		$attributes[ self::ATTR_DATA_WPFS_FORM_TYPE ] = MM_WPFS::FORM_TYPE_INLINE_SAVE_CARD;

		return $attributes;
	}

}

class MM_WPFS_PopupPaymentFormView extends MM_WPFS_PaymentFormView {

	use MM_WPFS_PopupFormView;

	/**
	 * MM_WPFS_PopupPaymentFormView constructor.
	 *
	 * @param $form
	 */
	public function __construct( $form ) {
		parent::__construct( $form );

		$this->action->setValue( self::FIELD_ACTION_VALUE_POPUP_PAYMENT_CHARGE );
	}

	public static function getFields() {
		$fields = self::getPopupBillingAddressFields();

		return array_merge( $fields, parent::getFields() );
	}

	protected function getFormAttributes() {
		$attributes = array();

		$attributes[ self::ATTR_DATA_WPFS_FORM_TYPE ] = MM_WPFS::FORM_TYPE_POPUP_PAYMENT;
		$attributes                                   = array_merge( $attributes, parent::getFormAttributes() );

		$popupFormAttributes = $this->getPopupFormAttributes( $this->form );

		return array_merge( $attributes, $popupFormAttributes );
	}

}

class MM_WPFS_PopupSubscriptionFormView extends MM_WPFS_SubscriptionFormView {

	use MM_WPFS_PopupFormView;

	/**
	 * @var MM_WPFS_Control
	 */
	protected $customVATBillingCountry;

	/**
	 * MM_WPFS_PopupSubscriptionFormView constructor.
	 *
	 * @param $form
	 * @param $stripePlans
	 */
	public function __construct( $form, $stripePlans ) {
		parent::__construct( $form, $stripePlans );

		$this->action->setValue( self::FIELD_ACTION_VALUE_POPUP_SUBSCRIPTION_CHARGE );
		$this->customVATBillingCountry = null;
		if ( 0 == $this->form->simpleButtonLayout && MM_WPFS::VAT_RATE_TYPE_CUSTOM_VAT == $this->form->vatRateType && 1 == $this->form->showBillingAddress ) {
			$this->customVATBillingCountry = MM_WPFS_ControlUtils::createControl( $this->formHash, self::FIELD_BILLING_ADDRESS_COUNTRY, null, null, __( 'Country', MM_WPFS::L10N_DOMAIN_PUBLIC ), null );
			$countryOptions                = array();
			$selectCountryOption           = MM_WPFS_ControlUtils::createControl( $this->formHash, null, null,
				/* translators: Option label for the default option of the country selector dropdown */
				__( 'Select country', MM_WPFS::L10N_DOMAIN_PUBLIC ), null, null );
			$selectCountryOption->setAttributes(
				array(
					'disabled' => 'disabled'
				)
			);
			array_push( $countryOptions, $selectCountryOption );
			$defaultCustomVATBillingCountrySelected = false;
			foreach ( MM_WPFS_Countries::get_available_countries() as $countryKey => $country ) {
				$option = MM_WPFS_ControlUtils::createControl( $this->formHash, null, null, MM_WPFS_Localization::translateLabel( $country['name'] ), null, null );
				if ( isset( $this->form->defaultBillingCountry ) && $this->form->defaultBillingCountry === $country['alpha-2'] ) {
					$defaultCustomVATBillingCountrySelected = true;
					$option->setAttributes( array( 'selected' => 'selected' ) );
				}
				$option->setValue( $countryKey );
				array_push( $countryOptions, $option );
			}
			$this->customVATBillingCountry->setOptions( $countryOptions );
			if ( false === $defaultCustomVATBillingCountrySelected ) {
				$selectCountryOption->setAttributes(
					array(
						'disabled' => 'disabled',
						'selected' => 'selected'
					)
				);
			}
			if ( isset( $this->form->vatRateType ) ) {
				$this->customVATBillingCountry->setAttributes( array( self::ATTR_DATA_WPFS_VAT_RATE_TYPE => $this->form->vatRateType ) );
			}
		}

	}

	public static function getFields() {
		$fields = array(
			self::FIELD_BILLING_ADDRESS_COUNTRY => MM_WPFS_ControlUtils::selectMenu( self::FIELD_BILLING_ADDRESS_COUNTRY )
		);
		$fields = array_merge( $fields, self::getPopupBillingAddressFields() );

		return array_merge( $fields, parent::getFields() );
	}

	/**
	 * @return array
	 */
	public function getFormAttributes() {

		$attributes = array();

		$attributes[ self::ATTR_DATA_WPFS_FORM_TYPE ]            = MM_WPFS::FORM_TYPE_POPUP_SUBSCRIPTION;
		$attributes[ self::ATTR_DATA_WPFS_SIMPLE_BUTTON_LAYOUT ] = $this->form->simpleButtonLayout;
		$attributes                                              = array_merge( $attributes, parent::getFormAttributes() );

		$popupFormAttributes = $this->getPopupFormAttributes( $this->form );

		if ( 1 == $this->form->simpleButtonLayout ) {
			$attributes[ self::ATTR_CLASS ] = self::ATTR_CLASS_VALUE_WPFS_FORM_WPFS_FORM_INLINE;
		}

		return array_merge( $attributes, $popupFormAttributes );
	}

	public function customVATBillingCountry() {
		return $this->customVATBillingCountry;
	}

	public function getFirstPlanName() {
		$firstPlanName = null;

		if ( isset( $this->form ) && isset( $this->form->plans ) ) {
			$plans = json_decode( $this->form->plans );
			if ( JSON_ERROR_NONE === json_last_error() ) {
				if ( is_array( $plans ) && count( $plans ) > 0 ) {
					$firstPlanName = $plans[0];
				}
			}
		}

		return $firstPlanName;
	}

}

class MM_WPFS_PopupCardCaptureFormView extends MM_WPFS_PopupPaymentFormView {

	use MM_WPFS_PopupFormView;

	/**
	 * MM_WPFS_PopupCardCaptureFormView constructor.
	 *
	 * @param $form
	 */
	public function __construct( $form ) {
		parent::__construct( $form );
	}

	public function getFormAttributes() {
		$attributes = parent::getFormAttributes();

		$attributes[ self::ATTR_DATA_WPFS_FORM_TYPE ] = MM_WPFS::FORM_TYPE_POPUP_SAVE_CARD;

		return $attributes;
	}

}
