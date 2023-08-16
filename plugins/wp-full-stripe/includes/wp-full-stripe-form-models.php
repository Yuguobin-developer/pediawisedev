<?php

/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2018.05.31.
 * Time: 16:55
 */
trait MM_WPFS_Model {

	/**
	 * @var MM_WPFS_Validator
	 */
	protected $__validator;

	/**
	 * @param $parameterName
	 * @param null $defaultValue
	 * @param string $sanitationType
	 *
	 * @return string
	 */
	public function getSanitizedPostParam( $parameterName, $defaultValue = null, $sanitationType = MM_WPFS_ModelConstants::SANITATION_TYPE_TEXT_FIELD ) {
		return $this->getSanitizedArrayParam( $_POST, $parameterName, $defaultValue, $sanitationType );
	}

	/**
	 * @param array $dataArray
	 * @param $parameterName
	 * @param null $defaultValue
	 * @param string $sanitationType
	 *
	 * @return array|string
	 */
	public function getSanitizedArrayParam( $dataArray, $parameterName, $defaultValue = null, $sanitationType = MM_WPFS_ModelConstants::SANITATION_TYPE_TEXT_FIELD ) {
		$parameterValue = $this->getArrayParam( $dataArray, $parameterName, $defaultValue );

		return $this->sanitizeValue( $parameterValue, $sanitationType );
	}

	/**
	 * @param array $dataArray
	 * @param $parameterName
	 * @param null $defaultValue
	 *
	 * @return array|string
	 */
	public function getArrayParam( $dataArray, $parameterName, $defaultValue = null ) {
		if ( array_key_exists( $parameterName, $dataArray ) && isset( $dataArray[ $parameterName ] ) ) {
			$value = wp_unslash( $dataArray[ $parameterName ] );
		} else {
			$value = wp_unslash( $defaultValue );
		}

		return $value;
	}

	/**
	 * @param $value
	 * @param $sanitationType
	 *
	 * @return array|string
	 */
	public function sanitizeValue( $value, $sanitationType = MM_WPFS_ModelConstants::SANITATION_TYPE_TEXT_FIELD ) {
		if ( is_array( $value ) ) {
			if ( MM_WPFS_ModelConstants::SANITATION_TYPE_TEXT_FIELD === $sanitationType ) {
				$functionName = 'sanitize_text_field';
			} elseif ( MM_WPFS_ModelConstants::SANITATION_TYPE_EMAIL === $sanitationType ) {
				$functionName = 'sanitize_email';
			} elseif ( MM_WPFS_ModelConstants::SANITATION_TYPE_KEY === $sanitationType ) {
				$functionName = 'sanitize_key';
			} else {
				$functionName = 'sanitize_text_field';
			}

			array_walk_recursive( $value, $functionName );

			return $value;
		} else {
			if ( MM_WPFS_ModelConstants::SANITATION_TYPE_TEXT_FIELD === $sanitationType ) {
				return sanitize_text_field( $value );
			} elseif ( MM_WPFS_ModelConstants::SANITATION_TYPE_EMAIL === $sanitationType ) {
				return sanitize_email( $value );
			} elseif ( MM_WPFS_ModelConstants::SANITATION_TYPE_KEY === $sanitationType ) {
				return sanitize_key( $value );
			} else {
				return sanitize_text_field( $value );
			}
		}
	}

	/**
	 * This function retrieves the value saved on the specific key from the $_POST array.
	 * The function strips slashes from the returned value.
	 *
	 * @see wp_unslash()
	 *
	 * @param $parameterName
	 * @param null $defaultValue
	 *
	 * @return null
	 */
	public function getPostParam( $parameterName, $defaultValue = null ) {
		return $this->getArrayParam( $_POST, $parameterName, $defaultValue );
	}

	/**
	 * @param $parameterName
	 * @param null $defaultValue
	 *
	 * @return null
	 */
	public function getNumericPostParam( $parameterName, $defaultValue = null ) {
		return $this->getNumericArrayParam( $_POST, $parameterName, $defaultValue );
	}

	/**
	 * @param array $dataArray
	 * @param $parameterName
	 * @param null $defaultValue
	 *
	 * @return array|string
	 */
	public function getNumericArrayParam( $dataArray, $parameterName, $defaultValue = null ) {
		if ( isset( $dataArray[ $parameterName ] ) && is_numeric( $dataArray[ $parameterName ] ) ) {
			$value = wp_unslash( $dataArray[ $parameterName ] );
		} else {
			$value = wp_unslash( $defaultValue );
		}

		return $value;
	}

	/**
	 * @deprecated
	 *
	 * @param $parameterName
	 * @param null $defaultValue
	 *
	 * @return string
	 */
	public function getStrippedPostParam( $parameterName, $defaultValue = null ) {
		return $this->getStrippedArrayParam( $_POST, $parameterName, $defaultValue );
	}

	/**
	 * @param array $dataArray
	 * @param $parameterName
	 * @param null $defaultValue
	 *
	 * @return string
	 */
	public function getStrippedArrayParam( $dataArray, $parameterName, $defaultValue = null ) {
		return stripslashes( $this->getArrayParam( $dataArray, $parameterName, $defaultValue ) );
	}

	/**
	 * @param $parameterName
	 * @param null $defaultValue
	 *
	 * @return string
	 */
	public function getHTMLDecodedPostParam( $parameterName, $defaultValue = null ) {
		return $this->getHTMLDecodedArrayParam( $_POST, $parameterName, $defaultValue );
	}

	/**
	 * @param array $dataArray
	 * @param $parameterName
	 * @param null $defaultValue
	 *
	 * @return string
	 */
	public function getHTMLDecodedArrayParam( $dataArray, $parameterName, $defaultValue = null ) {
		return html_entity_decode( $this->getArrayParam( $dataArray, $parameterName, $defaultValue ) );
	}

	/**
	 * @param $parameterName
	 *
	 * @return array|mixed|object
	 */
	public function getJSONDecodedPostParam( $parameterName ) {
		return $this->getJSONDecodedArrayParam( $_POST, $parameterName );
	}

	/**
	 * @param array $dataArray
	 * @param $parameterName
	 *
	 * @return array|mixed|object
	 */
	public function getJSONDecodedArrayParam( $dataArray, $parameterName ) {
		return json_decode( rawurldecode( stripslashes( $dataArray[ $parameterName ] ) ) );
	}

	/**
	 * @param $parameterName
	 * @param string $sanitationType
	 *
	 * @return string
	 */
	public function getURLDecodedPostParam( $parameterName, $sanitationType = MM_WPFS_ModelConstants::SANITATION_TYPE_TEXT_FIELD ) {
		return $this->getURLDecodedArrayParam( $_POST, $parameterName, $sanitationType );
	}

	/**
	 * @param array $dataArray
	 * @param $parameterName
	 * @param string $sanitationType
	 *
	 * @return array|string
	 */
	public function getURLDecodedArrayParam( $dataArray, $parameterName, $sanitationType = MM_WPFS_ModelConstants::SANITATION_TYPE_TEXT_FIELD ) {
		return $this->sanitizeValue( urldecode( $this->getArrayParam( $dataArray, $parameterName ) ), $sanitationType );
	}

}

trait MM_WPFS_Admin_InlineFormModel {

	use MM_WPFS_Model;

	protected $title;
	// todo tnagy rename to showBillingAddress later
	protected $showAddress;
	protected $showShippingAddress;
    protected $preferredLanguage;

	protected function bindInlineParams( $dataArray ) {

		// tnagy WPFS-740: remove form title
		// $this->title       = $this->getSanitizedPostParam( MM_WPFS_Admin_InlineForm::PARAM_FORM_TITLE );
		$this->title               = '';
		$this->showAddress         = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_InlineForm::PARAM_FORM_SHOW_ADDRESS_INPUT, 0 );
		$this->showShippingAddress = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_InlineForm::PARAM_FORM_SHOW_SHIPPING_ADDRESS_INPUT, 0 );
        $this->preferredLanguage   = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_InlineForm::PARAM_FORM_PREFERRED_LANGUAGE, MM_WPFS_Admin_InlineForm::DEFAULT_PREFERRED_LANGUAGE );

		if ( 1 == $this->showShippingAddress ) {
			$this->showAddress = 1;
		}
	}

	protected function getInlineDataArray() {

		$data = array(
			'formTitle'           => $this->title,
			'showAddress'         => $this->showAddress,
			'showShippingAddress' => $this->showShippingAddress,
			'preferredLanguage'   => $this->preferredLanguage
		);

		return $data;
	}

}

trait MM_WPFS_Admin_PopupFormModel {

	use MM_WPFS_Model;

	protected $companyName;
	protected $productDescription;
	protected $openButtonText;
	protected $showBillingAddress;
	protected $showShippingAddress;
	protected $showRememberMe;
	protected $image;
	protected $disableStyling;
	protected $preferredLanguage;

	protected function bindPopupParams( $dataArray ) {

		// No need to localize 'My Form' anymore, it's not displayed on UI
		$this->companyName         = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_PopupForm::PARAM_COMPANY_NAME, 'My Form' );
		$this->productDescription  = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_PopupForm::PARAM_PROD_DESC, self::getDefaultProductDescription() );
		$this->openButtonText      = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_PopupForm::PARAM_OPEN_FORM_BUTTON_TEXT );
		$this->showBillingAddress  = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_PopupForm::PARAM_FORM_SHOW_ADDRESS_INPUT, 0 );
		$this->showShippingAddress = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_PopupForm::PARAM_FORM_SHOW_SHIPPING_ADDRESS_INPUT, 0 );
		$this->showRememberMe      = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_PopupForm::PARAM_FORM_SHOW_REMEMBER_ME, 0 );
		$this->image               = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_PopupForm::PARAM_FORM_CHECKOUT_IMAGE, '' );
		$this->disableStyling      = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_PopupForm::PARAM_FORM_DISABLE_STYLING, 0 );
		$this->preferredLanguage   = $this->getSanitizedArrayParam( $dataArray, MM_WPFS_Admin_PopupForm::PARAM_FORM_PREFERRED_LANGUAGE, MM_WPFS_Admin_PopupForm::DEFAULT_PREFERRED_LANGUAGE );

	}

	public static function getDefaultProductDescription() {
		/* translators: Placeholder product name for newly created one-time payment forms */
		return __( 'My Product', MM_WPFS::L10N_DOMAIN_PUBLIC );
	}

	protected function getPopupData() {

		$data = array(
			'companyName'         => $this->companyName,
			'productDesc'         => $this->productDescription,
			'openButtonTitle'     => $this->openButtonText,
			'showBillingAddress'  => $this->showBillingAddress,
			'showShippingAddress' => $this->showShippingAddress,
			'showRememberMe'      => $this->showRememberMe,
			'image'               => $this->image,
			'disableStyling'      => $this->disableStyling,
			'preferredLanguage'   => $this->preferredLanguage
		);

		return $data;
	}

}

interface MM_WPFS_ModelConstants {

	const SANITATION_TYPE_TEXT_FIELD = 'text_field';
	const SANITATION_TYPE_KEY = 'key';
	const SANITATION_TYPE_EMAIL = 'email';

}

/**
 * Interface MM_WPFS_Binder contains the necessary functions for implementation to bind data to properties.
 */
interface MM_WPFS_Binder {

	const EMPTY_STR = '';

	/**
	 * Performs property binding by he $_POST superglobal.
	 *
	 * @return MM_WPFS_BindingResult
	 */
	public function bind();

	/**
	 * Performs property binding by the given array.
	 *
	 * @param $postData
	 *
	 * @return MM_WPFS_BindingResult
	 */
	public function bindByArray( $postData );

	/**
	 * This function can be overridden/implemented to call functions for specific operations that need to be run after
	 * bind().
	 *
	 * @return mixed
	 */
	public function afterBind();

	/**
	 * Returns an array with property names as keys to save this instance to database
	 *
	 * @return array
	 */
	public function getData();

	/**
	 * Returns an array with POST parameters as keys to serialize this instance. This array should be used as a
	 * parameter for bindByArray() later.
	 *
	 * @see MM_WPFS_Binder::bindByArray()
	 * @return array
	 */
	public function getPostData();

}

interface MM_WPFS_Admin_PopupForm {

	const PARAM_COMPANY_NAME = 'company_name';
	const PARAM_PROD_DESC = 'prod_desc';
	const PARAM_OPEN_FORM_BUTTON_TEXT = 'open_form_button_text';
	const PARAM_FORM_SHOW_ADDRESS_INPUT = 'form_show_address_input';
	const PARAM_FORM_SHOW_SHIPPING_ADDRESS_INPUT = 'form_show_shipping_address_input';
	const PARAM_FORM_SHOW_REMEMBER_ME = 'form_show_remember_me';
	const PARAM_FORM_CHECKOUT_IMAGE = 'form_checkout_image';
	const PARAM_FORM_DISABLE_STYLING = 'form_disable_styling';
	const PARAM_FORM_PREFERRED_LANGUAGE = 'form_preferred_language';
	const DEFAULT_PREFERRED_LANGUAGE = 'auto';

}

interface MM_WPFS_Admin_InlineForm {

	const PARAM_FORM_TITLE = 'form_title';
	const PARAM_FORM_SHOW_ADDRESS_INPUT = 'form_show_address_input';
	const PARAM_FORM_SHOW_SHIPPING_ADDRESS_INPUT = 'form_show_shipping_address_input';
    const PARAM_FORM_PREFERRED_LANGUAGE = 'form_preferred_language';
    const DEFAULT_PREFERRED_LANGUAGE = 'auto';

}

interface MM_WPFS_Public_PopupForm {

}

interface MM_WPFS_Public_InlineForm {

}

class MM_WPFS_BindingResult {

	protected $formHash = null;
	protected $globalErrors = array();
	protected $fieldErrors = array();

	/**
	 * MM_WPFS_BindingResult constructor.
	 *
	 * @param $formHash
	 */
	public function __construct( $formHash = null ) {
		$this->formHash = $formHash;
	}

	public function hasErrors() {
		return ! empty( $this->globalErrors ) || ! empty( $this->fieldErrors );
	}

	public function hasFieldErrors( $field = null ) {
		if ( is_null( $field ) ) {
			return ! empty( $this->fieldErrors );
		} else {
			return array_key_exists( $field, $this->fieldErrors );
		}
	}

	public function addFieldError( $fieldName, $fieldId, $error ) {
		if ( is_null( $fieldName ) ) {
			return;
		}
		if ( ! array_key_exists( $fieldName, $this->fieldErrors ) ) {
			$this->fieldErrors[ $fieldName ] = array();
		}
		array_push(
			$this->fieldErrors[ $fieldName ],
			array(
				'id'      => $fieldId,
				'name'    => $fieldName,
				'message' => $error
			)
		);
	}

	public function getFieldErrors( $field = null ) {
		if ( is_null( $field ) ) {
			$fieldErrors = array();
			foreach ( array_values( $this->fieldErrors ) as $errors ) {
				$fieldErrors = array_merge( $fieldErrors, $errors );
			}

			return $fieldErrors;
		}
		if ( array_key_exists( $field, $this->fieldErrors ) ) {
			return $this->fieldErrors[ $field ];
		} else {
			return array();
		}
	}

	public function getGlobalErrors() {
		return $this->globalErrors;
	}

	public function hasGlobalErrors() {
		return ! empty( $this->globalErrors );
	}

	public function addGlobalError( $error ) {
		array_push( $this->globalErrors, $error );
	}

	/**
	 * @return null
	 */
	public function getFormHash() {
		return $this->formHash;
	}

	/**
	 * @param null $formHash
	 */
	public function setFormHash( $formHash ) {
		$this->formHash = $formHash;
	}

}

abstract class MM_WPFS_Admin_FormModel implements MM_WPFS_Binder {

	use MM_WPFS_Model;

	const PARAM_FORM_ID = 'formID';
	const PARAM_FORM_NAME = 'form_name';
	const PARAM_FORM_BUTTON_TEXT = 'form_button_text';
	const PARAM_FORM_INCLUDE_CUSTOM_INPUT = 'form_include_custom_input';
	const PARAM_FORM_CUSTOM_INPUT_REQUIRED = 'form_custom_input_required';
	const PARAM_CUSTOM_INPUTS = 'customInputs';
	const PARAM_FORM_DO_REDIRECT = 'form_do_redirect';
	const PARAM_FORM_REDIRECT_TO = 'form_redirect_to';
	const PARAM_FORM_REDIRECT_PAGE_OR_POST_ID = 'form_redirect_page_or_post_id';
	const PARAM_FORM_REDIRECT_URL = 'form_redirect_url';
	const PARAM_SHOW_DETAILED_SUCCESS_PAGE = 'showDetailedSuccessPage';
	const PARAM_STRIPE_DESCRIPTION = 'stripe_description';
	const PARAM_SHOW_TERMS_OF_USE = 'show_terms_of_use';
	const PARAM_TERMS_OF_USE_LABEL = 'terms_of_use_label';
	const PARAM_TERMS_OF_USE_NOT_CHECKED_ERROR_MESSAGE = 'terms_of_use_not_checked_error_message';
	const PARAM_FORM_SEND_EMAIL_RECEIPT = 'form_send_email_receipt';
	const PARAM_FORM_DEFAULT_BILLING_COUNTRY = 'form_default_billing_country';
	const PARAM_DECIMAL_SEPARATOR = 'decimal_separator';
	const PARAM_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE = 'show_currency_symbol_instead_of_code';
	const PARAM_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION = 'show_currency_sign_at_first_position';
	const PARAM_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT = 'put_whitespace_between_currency_and_amount';

	const REDIRECT_TO_PAGE_OR_POST = 'page_or_post';
	const REDIRECT_TO_URL = 'url';

	protected $id;
	protected $name;
	protected $buttonTitle;
	protected $showCustomInput;
	protected $customInputRequired;
	protected $customInputs;
	protected $doRedirect;
	/**
	 * @transient
	 */
	protected $redirectTo;
	protected $redirectPostID;
	protected $redirectUrl;
	protected $redirectToPageOrPost;
	protected $showDetailedSuccessPage;
	protected $stripeDescription;
	protected $showTermsOfUse;
	protected $termsOfUseLabel;
	protected $termsOfUseNotCheckedErrorMessage;
	protected $sendEmailReceipt;
	protected $decimalSeparator;
	protected $showCurrencySymbolInsteadOfCode;
	protected $showCurrencySignAtFirstPosition;
	protected $putWhitespaceBetweenCurrencyAndAmount;

	public function bind() {
		return $this->bindByArray( $_POST );
	}

	public function bindByArray( $postData ) {

		$bindingResult = new MM_WPFS_BindingResult();

		$this->id                  = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_ID );
		$this->name                = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_NAME );
		$this->buttonTitle         = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_BUTTON_TEXT,
			/* translators: Default payment button text on one-time payment forms */
			__( 'Pay', MM_WPFS::L10N_DOMAIN_PUBLIC ) );
		$this->showCustomInput     = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_INCLUDE_CUSTOM_INPUT );
		$this->customInputRequired = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_CUSTOM_INPUT_REQUIRED, 0 );
		if ( $this->showCustomInput == 0 ) {
			$this->customInputRequired = 0;
		}
		$this->customInputs         = $this->getSanitizedArrayParam( $postData, self::PARAM_CUSTOM_INPUTS );
		$this->doRedirect           = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_DO_REDIRECT );
		$this->redirectTo           = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_REDIRECT_TO );
		$this->redirectPostID       = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_REDIRECT_PAGE_OR_POST_ID, 0 );
		$this->redirectUrl          = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_REDIRECT_URL );
		$this->redirectUrl          = MM_WPFS_Utils::add_http_prefix( $this->redirectUrl );
		$this->redirectToPageOrPost = 1;
		if ( self::REDIRECT_TO_PAGE_OR_POST === $this->redirectTo ) {
			$this->redirectToPageOrPost = 1;
		} else if ( self::REDIRECT_TO_URL === $this->redirectTo ) {
			$this->redirectToPageOrPost = 0;
		}
		$this->showDetailedSuccessPage          = $this->getSanitizedArrayParam( $postData, self::PARAM_SHOW_DETAILED_SUCCESS_PAGE, 0 );
		$this->stripeDescription                = $this->getSanitizedArrayParam( $postData, self::PARAM_STRIPE_DESCRIPTION );
		$this->showTermsOfUse                   = $this->getSanitizedArrayParam( $postData, self::PARAM_SHOW_TERMS_OF_USE, 0 );
		$this->termsOfUseLabel                  = $this->getArrayParam( $postData, self::PARAM_TERMS_OF_USE_LABEL );
		$this->termsOfUseNotCheckedErrorMessage = $this->getSanitizedArrayParam( $postData, self::PARAM_TERMS_OF_USE_NOT_CHECKED_ERROR_MESSAGE );
		$this->sendEmailReceipt                 = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_SEND_EMAIL_RECEIPT, 0 );
		$decimalSeparator                       = $this->getSanitizedArrayParam( $postData, self::PARAM_DECIMAL_SEPARATOR );
		if ( MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT === $decimalSeparator ) {
			$this->decimalSeparator = MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT;
		} elseif ( MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_COMMA === $decimalSeparator ) {
			$this->decimalSeparator = MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_COMMA;
		} else {
			$this->decimalSeparator = MM_WPFS::DECIMAL_SEPARATOR_SYMBOL_DOT;
		}
		$this->showCurrencySymbolInsteadOfCode       = $this->getSanitizedArrayParam( $postData, self::PARAM_SHOW_CURRENCY_SYMBOL_INSTEAD_OF_CODE, 1 );
		$this->showCurrencySignAtFirstPosition       = $this->getSanitizedArrayParam( $postData, self::PARAM_SHOW_CURRENCY_SIGN_AT_FIRST_POSITION, 1 );
		$this->putWhitespaceBetweenCurrencyAndAmount = $this->getSanitizedArrayParam( $postData, self::PARAM_PUT_WHITESPACE_BETWEEN_CURRENCY_AND_AMOUNT, 0 );

		return $bindingResult;
	}

	public function getData() {

		$data = array(
			'name'                                  => $this->name,
			'buttonTitle'                           => $this->buttonTitle,
			'showCustomInput'                       => $this->showCustomInput,
			'customInputRequired'                   => $this->customInputRequired,
			'customInputs'                          => $this->customInputs,
			'redirectOnSuccess'                     => $this->doRedirect,
			'redirectPostID'                        => $this->redirectPostID,
			'redirectUrl'                           => $this->redirectUrl,
			'redirectToPageOrPost'                  => $this->redirectToPageOrPost,
			'showDetailedSuccessPage'               => $this->showDetailedSuccessPage,
			'stripeDescription'                     => $this->stripeDescription,
			'showTermsOfUse'                        => $this->showTermsOfUse,
			'termsOfUseLabel'                       => $this->termsOfUseLabel,
			'termsOfUseNotCheckedErrorMessage'      => $this->termsOfUseNotCheckedErrorMessage,
			'sendEmailReceipt'                      => $this->sendEmailReceipt,
			'decimalSeparator'                      => $this->decimalSeparator,
			'showCurrencySymbolInsteadOfCode'       => $this->showCurrencySymbolInsteadOfCode,
			'showCurrencySignAtFirstPosition'       => $this->showCurrencySignAtFirstPosition,
			'putWhitespaceBetweenCurrencyAndAmount' => $this->putWhitespaceBetweenCurrencyAndAmount
		);

		return $data;
	}

	public function getPostData() {
		// tnagy unsupported operation
		return array();
	}

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

}

abstract class MM_WPFS_Admin_PaymentFormModel extends MM_WPFS_Admin_FormModel {

	const PARAM_FORM_CURRENCY = 'form_currency';
	const PARAM_FORM_AMOUNT = 'form_amount';
	const PARAM_FORM_CUSTOM = 'form_custom';
	const PARAM_PAYMENT_AMOUNT_VALUES = 'payment_amount_values';
	const PARAM_PAYMENT_AMOUNT_DESCRIPTIONS = 'payment_amount_descriptions';
	const PARAM_ALLOW_CUSTOM_PAYMENT_AMOUNT = 'allow_custom_payment_amount';
	const PARAM_FORM_CHARGE_TYPE = 'form_charge_type';
	const PARAM_FORM_BUTTON_AMOUNT = 'form_button_amount';
	const PARAM_AMOUNT_SELECTOR_STYLE = 'amount_selector_style';

	protected $currency;
	protected $amount;
	protected $custom;
	protected $listOfAmounts;
	protected $allowListOfAmountCustom;
	protected $chargeType;
	protected $showButtonAmount;
	protected $amountSelectorStyle;

	public function bindByArray( $postData ) {

		$bindingResult = parent::bindByArray( $postData );

		$this->currency                = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_CURRENCY );
		$this->amount                  = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_AMOUNT, 0 );
		$this->custom                  = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_CUSTOM );
		$this->listOfAmounts           = null;
		$this->allowListOfAmountCustom = 0;
		if ( $this->custom == MM_WPFS::PAYMENT_TYPE_LIST_OF_AMOUNTS ) {
			$listOfAmounts             = array();
			$paymentAmountValues       = explode( ',', $this->getSanitizedArrayParam( $postData, self::PARAM_PAYMENT_AMOUNT_VALUES ) );
			$paymentAmountDescriptions = explode( ',', $this->getURLDecodedArrayParam( $postData, self::PARAM_PAYMENT_AMOUNT_DESCRIPTIONS ) );
			for ( $i = 0; $i < count( $paymentAmountValues ); $i ++ ) {
				$listElement = array( $paymentAmountValues[ $i ], $paymentAmountDescriptions[ $i ] );
				array_push( $listOfAmounts, $listElement );
			}
			$this->listOfAmounts           = json_encode( $listOfAmounts );
			$this->allowListOfAmountCustom = $this->getSanitizedArrayParam( $postData, self::PARAM_ALLOW_CUSTOM_PAYMENT_AMOUNT, 0 );
		}
		$this->chargeType          = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_CHARGE_TYPE, MM_WPFS::CHARGE_TYPE_IMMEDIATE );
		$this->showButtonAmount    = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_BUTTON_AMOUNT, 0 );
		$this->amountSelectorStyle = $this->getSanitizedArrayParam( $postData, self::PARAM_AMOUNT_SELECTOR_STYLE, MM_WPFS::AMOUNT_SELECTOR_STYLE_DROPDOWN );

		return $bindingResult;
	}

	public function getData() {

		$parentData = parent::getData();

		$data = array(
			'currency'                 => $this->currency,
			'amount'                   => $this->amount,
			'customAmount'             => $this->custom,
			'listOfAmounts'            => $this->listOfAmounts,
			'allowListOfAmountsCustom' => $this->allowListOfAmountCustom,
			'chargeType'               => $this->chargeType,
			'showButtonAmount'         => $this->showButtonAmount,
			'amountSelectorStyle'      => $this->amountSelectorStyle
		);

		$data = array_merge( $data, $parentData );

		return $data;
	}


	/**
	 * Updates properties to act as a saved card form
	 */
	public function convertToCardCaptureForm() {
		$this->currency                = MM_WPFS::CURRENCY_USD;
		$this->amount                  = 0;
		$this->custom                  = MM_WPFS::PAYMENT_TYPE_CARD_CAPTURE;
		$this->listOfAmounts           = null;
		$this->allowListOfAmountCustom = 0;
		$this->showButtonAmount        = 0;
		$this->amountSelectorStyle     = MM_WPFS::AMOUNT_SELECTOR_STYLE_DROPDOWN;
	}

}

abstract class MM_WPFS_Admin_DonationFormModel extends MM_WPFS_Admin_FormModel {

    const PARAM_FORM_CURRENCY = 'form_currency';
    const PARAM_DONATION_AMOUNT_VALUES = 'donation_amount_values';
    const PARAM_ALLOW_CUSTOM_DONATION_AMOUNT = 'allow_custom_donation_amount';
    const PARAM_ALLOW_DAILY_RECURRING = 'allow_daily_recurring';
    const PARAM_ALLOW_WEEKLY_RECURRING = 'allow_weekly_recurring';
    const PARAM_ALLOW_MONTHLY_RECURRING = 'allow_monthly_recurring';
    const PARAM_ALLOW_ANNUAL_RECURRING = 'allow_annual_recurring';

    protected $currency;
    protected $donationAmounts;
    protected $allowCustomDonationAmount;
    protected $allowDailyRecurring;
    protected $allowWeeklyRecurring;
    protected $allowMonthlyRecurring;
    protected $allowAnnualRecurring;

    public function bindByArray( $postData ) {

        $bindingResult = parent::bindByArray( $postData );

        $this->currency                  = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_CURRENCY );

        $donationAmountValues            = explode( ',', $this->getSanitizedArrayParam( $postData, self::PARAM_DONATION_AMOUNT_VALUES ) );
        $this->donationAmounts           = json_encode( $donationAmountValues );
        $this->allowCustomDonationAmount = $this->getSanitizedArrayParam( $postData, self::PARAM_ALLOW_CUSTOM_DONATION_AMOUNT, 0 );
        $this->allowDailyRecurring       = $this->getSanitizedArrayParam( $postData, self::PARAM_ALLOW_DAILY_RECURRING, 0 );
        $this->allowWeeklyRecurring      = $this->getSanitizedArrayParam( $postData, self::PARAM_ALLOW_WEEKLY_RECURRING, 0 );
        $this->allowMonthlyRecurring     = $this->getSanitizedArrayParam( $postData, self::PARAM_ALLOW_MONTHLY_RECURRING, 0 );
        $this->allowAnnualRecurring      = $this->getSanitizedArrayParam( $postData, self::PARAM_ALLOW_ANNUAL_RECURRING, 0 );

        return $bindingResult;
    }

    public function getData() {

        $parentData = parent::getData();

        $data = array(
            'currency'                      => $this->currency,
            'donationAmounts'               => $this->donationAmounts,
            'allowCustomDonationAmount'     => $this->allowCustomDonationAmount,
            'allowDailyRecurring'           => $this->allowDailyRecurring,
            'allowWeeklyRecurring'          => $this->allowWeeklyRecurring,
            'allowMonthlyRecurring'         => $this->allowMonthlyRecurring,
            'allowAnnualRecurring'          => $this->allowAnnualRecurring,
        );

        $data = array_merge( $data, $parentData );

        return $data;
    }
}

abstract class MM_WPFS_Admin_SubscriptionFormModel extends MM_WPFS_Admin_FormModel {

	const PARAM_FORM_INCLUDE_COUPON_INPUT = 'form_include_coupon_input';
	const PARAM_PLAN_ORDER = 'plan_order';
	const PARAM_SELECTED_PLANS = 'selected_plans';
	const PARAM_FORM_VAT_RATE_TYPE = 'form_vat_rate_type';
	const PARAM_FORM_VAT_PERCENT = 'form_vat_percent';
	const PARAM_PLAN_SELECTOR_STYLE = 'plan_selector_style';
	const PARAM_FORM_ALLOW_MULTIPLE_SUBSCRIPTIONS_INPUT = 'form_allow_multiple_subscriptions_input';
	const PARAM_FORM_MAXIMUM_QUANTITY_OF_SUBSCRIPTIONS = 'form_maximum_quantity_of_subscriptions';

	protected $includeCouponInput;
	protected $plans;
	protected $vatRateType;
	protected $vatPercent;
	protected $defaultBillingCountry;
	protected $planSelectorStyle;
	protected $allowMultipleSubscriptionsInput;
	protected $maximumQuantityOfSubscriptions;

	public function bindByArray( $postData ) {

		$bindingResult = parent::bindByArray( $postData );

		$this->includeCouponInput = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_INCLUDE_COUPON_INPUT, 0 );
		$planOrder                = $this->getJSONDecodedArrayParam( $postData, self::PARAM_PLAN_ORDER );
		$selectedPlans            = $this->getJSONDecodedArrayParam( $postData, self::PARAM_SELECTED_PLANS );
		$this->plans              = json_encode( $this->orderPlans( $selectedPlans, $planOrder ) );
		$this->vatRateType        = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_VAT_RATE_TYPE, MM_WPFS::VAT_RATE_TYPE_NO_VAT );
		$this->vatPercent         = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_VAT_PERCENT, 0.0 );
		if ( $this->vatRateType != MM_WPFS::VAT_RATE_TYPE_FIXED_VAT ) {
			$this->vatPercent = 0.0;
		}
		$this->defaultBillingCountry           = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_DEFAULT_BILLING_COUNTRY );
		$this->planSelectorStyle               = $this->getSanitizedArrayParam( $postData, self::PARAM_PLAN_SELECTOR_STYLE );
		$this->allowMultipleSubscriptionsInput = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_ALLOW_MULTIPLE_SUBSCRIPTIONS_INPUT, 0 );
		$this->maximumQuantityOfSubscriptions  = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_MAXIMUM_QUANTITY_OF_SUBSCRIPTIONS, 0 );

		return $bindingResult;
	}

	protected function orderPlans( $selectedPlansArray, $planOrderArray ) {
		$orderedPlans = array();
		if ( count( $selectedPlansArray ) > 0 ) {
			foreach ( $planOrderArray as $plan ) {
				if ( in_array( $plan, $selectedPlansArray ) ) {
					$orderedPlans[] = $plan;
				}
			}
		}

		return $orderedPlans;
	}

	public function getData() {
		$parentData = parent::getData();

		$data = array(
			'showCouponInput'                => $this->includeCouponInput,
			'plans'                          => $this->plans,
			'vatRateType'                    => $this->vatRateType,
			'vatPercent'                     => $this->vatPercent,
			'defaultBillingCountry'          => $this->defaultBillingCountry,
			'planSelectorStyle'              => $this->planSelectorStyle,
			'allowMultipleSubscriptions'     => $this->allowMultipleSubscriptionsInput,
			'maximumQuantityOfSubscriptions' => $this->maximumQuantityOfSubscriptions
		);

		$data = array_merge( $data, $parentData );

		return $data;
	}

}

class MM_WPFS_Admin_InlinePaymentFormModel extends MM_WPFS_Admin_PaymentFormModel implements MM_WPFS_Admin_InlineForm {

	use MM_WPFS_Admin_InlineFormModel;

	const PARAM_FORM_SHOW_EMAIL_INPUT = 'form_show_email_input';
	const PARAM_FORM_STYLE = 'form_style';

	protected $showEmailInput;
	protected $formStyle;
	protected $defaultBillingCountry;

	public function load( $dataArray ) {
		// tnagy unsupported operation
	}

	public function bindByArray( $postData ) {

		$bindingResult = parent::bindByArray( $postData );

		$this->bindInlineParams( $postData );

		$this->showEmailInput        = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_SHOW_EMAIL_INPUT, 1 );
		$this->formStyle             = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_STYLE );
		$this->defaultBillingCountry = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_DEFAULT_BILLING_COUNTRY );

		return $bindingResult;
	}

	public function getData() {

		$parentData = parent::getData();

		$inlineData = $this->getInlineDataArray();

		$data = array(
			'showEmailInput'        => $this->showEmailInput,
			'formStyle'             => $this->formStyle,
			'defaultBillingCountry' => $this->defaultBillingCountry
		);

		$data = array_merge( $data, $inlineData, $parentData );

		return $data;
	}

	public function afterBind() {

	}

}

class MM_WPFS_Admin_PopupPaymentFormModel extends MM_WPFS_Admin_PaymentFormModel {

	use MM_WPFS_Admin_PopupFormModel;

	const PARAM_FORM_USE_BITCOIN = 'form_use_bitcoin';
	const PARAM_FORM_USE_ALIPAY = 'form_use_alipay';

	protected $useBitcoin;
	protected $useAlipay;

	public function bindByArray( $postData ) {

		$bindingResult = parent::bindByArray( $postData );

		$this->bindPopupParams( $postData );

		$this->useBitcoin = 0; // $this->getNumericPostParam( MM_WPFS_PopupForm::PARAM_FORM_USE_BITCOIN, 0 );
		$this->useAlipay  = 0; // $this->getNumericPostParam( MM_WPFS_PopupForm::PARAM_FORM_USE_ALIPAY, 0 );

		if ( MM_WPFS::CURRENCY_USD !== $this->currency ) {
			$this->useAlipay  = 0;
			$this->useBitcoin = 0;
		}

		return $bindingResult;
	}

	public function getData() {

		$parentData = parent::getData();

		$popupData = $this->getPopupData();

		$data = array(
			'useBitcoin' => $this->useBitcoin,
			'useAlipay'  => $this->useAlipay
		);

		$data = array_merge( $data, $popupData, $parentData );

		return $data;
	}

	public function afterBind() {

	}

}

class MM_WPFS_Admin_InlineSubscriptionFormModel extends MM_WPFS_Admin_SubscriptionFormModel implements MM_WPFS_Admin_InlineForm {

	use MM_WPFS_Admin_InlineFormModel;

	const PARAM_ANCHOR_BILLING_CYCLE = 'form_anchor_billing_cycle_input';
	const PARAM_BILLING_CYCLE_ANCHOR_DAY = 'form_billing_cycle_anchor_day_input';
	const PARAM_PRORATE_UNTIL_ANCHOR_DAY = 'form_prorate_until_anchor_day_input';

	protected $anchorBillingCycle;
	protected $billingCycleAnchorDay;
	protected $prorateUntilAnchorDay;

	public function bindByArray( $postData ) {

		$bindingResult = parent::bindByArray( $postData );

		$this->bindInlineParams( $postData );

		if ( 1 == $this->showShippingAddress ) {
			$this->showAddress = 1;
		}
		if ( 0 == $this->showAddress ) {
			$this->defaultBillingCountry = null;
		}

		$this->anchorBillingCycle    = $this->getSanitizedArrayParam( $postData, self::PARAM_ANCHOR_BILLING_CYCLE, 0 );
		$this->billingCycleAnchorDay = $this->getSanitizedArrayParam( $postData, self::PARAM_BILLING_CYCLE_ANCHOR_DAY, 0 );
		$this->prorateUntilAnchorDay = $this->getSanitizedArrayParam( $postData, self::PARAM_PRORATE_UNTIL_ANCHOR_DAY, 0 );

		return $bindingResult;
	}

	public function getData() {

		$parentData = parent::getData();

		$inlineData = $this->getInlineDataArray();

		$data = array(
			'anchorBillingCycle'    => $this->anchorBillingCycle,
			'billingCycleAnchorDay' => $this->billingCycleAnchorDay,
			'prorateUntilAnchorDay' => $this->prorateUntilAnchorDay
		);

		$data = array_merge( $data, $inlineData, $parentData );

		return $data;
	}

	public function afterBind() {

	}

}

class MM_WPFS_Admin_PopupSubscriptionFormModel extends MM_WPFS_Admin_SubscriptionFormModel implements MM_WPFS_Admin_PopupForm {

	use MM_WPFS_Admin_PopupFormModel;

	const PARAM_FORM_SIMPLE_BUTTON_LAYOUT = 'form_simple_button_layout';

	protected $simpleButtonLayout;

	public function bindByArray( $postData ) {

		$bindingResult = parent::bindByArray( $postData );

		$this->bindPopupParams( $postData );

		// Legacy checkout parameters we cannot use in the new checkout
		$this->buttonTitle           =
			/* translators: Default payment button text on subscription forms */
			__( 'Subscribe', MM_WPFS::L10N_DOMAIN_PUBLIC );
		$this->defaultBillingCountry = null;

		$this->simpleButtonLayout = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_SIMPLE_BUTTON_LAYOUT, 0 );

		return $bindingResult;
	}

	public function getData() {

		$parentData = parent::getData();

		$popupData = $this->getPopupData();

		$data = array(
			'simpleButtonLayout' => $this->simpleButtonLayout
		);

		$data = array_merge( $data, $popupData, $parentData );

		return $data;
	}

	public function afterBind() {

	}

}


class MM_WPFS_Admin_InlineDonationFormModel extends MM_WPFS_Admin_DonationFormModel implements MM_WPFS_Admin_InlineForm {

    use MM_WPFS_Admin_InlineFormModel;

    protected $defaultBillingCountry;

    public function load( $dataArray ) {
        // tnagy unsupported operation
    }

    public function bindByArray( $postData ) {

        $bindingResult = parent::bindByArray( $postData );

        $this->bindInlineParams( $postData );

        $this->defaultBillingCountry = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_DEFAULT_BILLING_COUNTRY );

        return $bindingResult;
    }

    public function getData() {

        $parentData = parent::getData();

        $inlineData = $this->getInlineDataArray();

        $data = array(
            'defaultBillingCountry' => $this->defaultBillingCountry
        );

        $data = array_merge( $data, $inlineData, $parentData );

        return $data;
    }

    public function afterBind() {

    }

}

class MM_WPFS_Admin_PopupDonationFormModel extends MM_WPFS_Admin_DonationFormModel implements MM_WPFS_Admin_PopupForm {

    use MM_WPFS_Admin_PopupFormModel;

    protected $defaultBillingCountry;

    public function load( $dataArray ) {
        // tnagy unsupported operation
    }

    public function bindByArray( $postData ) {

        $bindingResult = parent::bindByArray( $postData );

        $this->bindPopupParams( $postData );

        $this->defaultBillingCountry = $this->getSanitizedArrayParam( $postData, self::PARAM_FORM_DEFAULT_BILLING_COUNTRY );

        return $bindingResult;
    }

    public function getData() {

        $parentData = parent::getData();

        $inlineData = $this->getPopupData();

        $data = array(
            'defaultBillingCountry' => $this->defaultBillingCountry
        );

        $data = array_merge( $data, $inlineData, $parentData );

        return $data;
    }

    public function afterBind() {

    }

    public static function getDefaultProductDescription() {
        /* translators: Placeholder product name for newly created checkout donation forms */
        return __( 'My Donation', MM_WPFS::L10N_DOMAIN_PUBLIC );
    }

}


abstract class MM_WPFS_Public_FormModel implements MM_WPFS_Binder {

	use MM_WPFS_Model;

	const ARRAY_KEY_ADDRESS_LINE_1 = 'line1';
	const ARRAY_KEY_ADDRESS_LINE_2 = 'line2';
	const ARRAY_KEY_ADDRESS_CITY = 'city';
	const ARRAY_KEY_ADDRESS_STATE = 'state';
	const ARRAY_KEY_ADDRESS_COUNTRY = 'country';
	const ARRAY_KEY_ADDRESS_COUNTRY_CODE = 'country_code';
	const ARRAY_KEY_ADDRESS_ZIP = 'zip';

	const PARAM_WPFS_FORM_NAME = 'wpfs-form-name';
	const PARAM_WPFS_FORM_ACTION = 'action';
	const PARAM_WPFS_FORM_GET_PARAMETERS = 'wpfs-form-get-parameters';
	const PARAM_WPFS_REFERRER = 'wpfs-referrer';
	/** @deprecated due to introducing PaymentIntent API */
	const PARAM_WPFS_STRIPE_TOKEN = 'wpfs-stripe-token';
	const PARAM_WPFS_STRIPE_PAYMENT_METHOD_ID = 'wpfs-stripe-payment-method-id';
	const PARAM_WPFS_STRIPE_PAYMENT_INTENT_ID = 'wpfs-stripe-payment-intent-id';
	const PARAM_WPFS_STRIPE_SETUP_INTENT_ID = 'wpfs-stripe-setup-intent-id';
	const PARAM_WPFS_CARD_HOLDER_NAME = 'wpfs-card-holder-name';
	const PARAM_WPFS_CARD_HOLDER_EMAIL = 'wpfs-card-holder-email';
	const PARAM_WPFS_CARD_HOLDER_PHONE = 'wpfs-card-holder-phone';
	const PARAM_WPFS_CUSTOM_INPUT = 'wpfs-custom-input';
	const PARAM_WPFS_SAME_BILLING_AND_SHIPPING_ADDRESS = 'wpfs-same-billing-and-shipping-address';
	const PARAM_WPFS_BILLING_NAME = 'wpfs-billing-name';
	const PARAM_WPFS_BILLING_ADDRESS_LINE_1 = 'wpfs-billing-address-line-1';
	const PARAM_WPFS_BILLING_ADDRESS_LINE_2 = 'wpfs-billing-address-line-2';
	const PARAM_WPFS_BILLING_ADDRESS_CITY = 'wpfs-billing-address-city';
	const PARAM_WPFS_BILLING_ADDRESS_STATE = 'wpfs-billing-address-state';
	const PARAM_WPFS_BILLING_ADDRESS_ZIP = 'wpfs-billing-address-zip';
	const PARAM_WPFS_BILLING_ADDRESS_COUNTRY = 'wpfs-billing-address-country';
	const PARAM_WPFS_SHIPPING_NAME = 'wpfs-shipping-name';
	const PARAM_WPFS_SHIPPING_ADDRESS_LINE_1 = 'wpfs-shipping-address-line-1';
	const PARAM_WPFS_SHIPPING_ADDRESS_LINE_2 = 'wpfs-shipping-address-line-2';
	const PARAM_WPFS_SHIPPING_ADDRESS_CITY = 'wpfs-shipping-address-city';
	const PARAM_WPFS_SHIPPING_ADDRESS_STATE = 'wpfs-shipping-address-state';
	const PARAM_WPFS_SHIPPING_ADDRESS_ZIP = 'wpfs-shipping-address-zip';
	const PARAM_WPFS_SHIPPING_ADDRESS_COUNTRY = 'wpfs-shipping-address-country';
	const PARAM_WPFS_TERMS_OF_USE_ACCEPTED = 'wpfs-terms-of-use-accepted';
	const PARAM_GOOGLE_RECAPTCHA_RESPONSE = 'g-recaptcha-response';
	const PARAM_WPFS_NONCE = 'wpfs-nonce';

	/** @var bool */
	protected $debugLog = false;
	protected $action;
	protected $formName;
	protected $formGetParameters;
	protected $referrer;
	/** @deprecated due to introducing PaymentIntent API */
	protected $stripeToken;
	protected $stripePaymentMethodId;
	protected $stripePaymentIntentId;
	protected $stripeSetupIntentId;
	protected $cardHolderName;
	protected $cardHolderEmail;
	protected $cardHolderPhone;
	protected $customInputValues;
	protected $sameBillingAndShippingAddress;
	protected $billingName;
	protected $billingAddressLine1;
	protected $billingAddressLine2;
	protected $billingAddressCity;
	protected $billingAddressState;
	protected $billingAddressZip;
	protected $billingAddressCountry;
	protected $shippingName;
	protected $shippingAddressLine1;
	protected $shippingAddressLine2;
	protected $shippingAddressCity;
	protected $shippingAddressState;
	protected $shippingAddressZip;
	protected $shippingAddressCountry;
	protected $termsOfUseAccepted;
	protected $googleReCaptchaResponse;
	protected $transactionId;
	protected $nonce;

	protected $__form;
	protected $__formHash;
	protected $__billingAddressCountryComposite;
	protected $__billingAddressCountryName;
	protected $__billingAddressCountryCode;
	protected $__shippingAddressCountryComposite;
	protected $__shippingAddressCountryName;
	protected $__shippingAddressCountryCode;
	/**
	 * @var \StripeWPFS\Customer
	 */
	protected $__stripeCustomer;
	protected $__productName;
	/**
	 * @var \StripeWPFS\PaymentMethod
	 */
	protected $__stripePaymentMethod;

	protected $__dao;

	/**
	 * MM_WPFS_Public_FormModel constructor.
	 */
	public function __construct() {
		$this->__dao = new MM_WPFS_Database();
	}

	public function bind() {
		return $this->bindByArray( $_POST );
	}

	public function bindByArray( $postData ) {
		$bindingResult = new MM_WPFS_BindingResult();

		$this->action                        = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_FORM_ACTION );
		$this->formName                      = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_FORM_NAME );
		$this->formGetParameters             = $this->getURLDecodedArrayParam( $postData, self::PARAM_WPFS_FORM_GET_PARAMETERS );
		$this->referrer                      = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_REFERRER );
		$this->stripeToken                   = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_STRIPE_TOKEN );
		$this->stripePaymentMethodId         = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_STRIPE_PAYMENT_METHOD_ID );
		$this->stripePaymentIntentId         = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_STRIPE_PAYMENT_INTENT_ID );
		$this->stripeSetupIntentId           = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_STRIPE_SETUP_INTENT_ID );
		$this->cardHolderName                = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_CARD_HOLDER_NAME );
		$this->cardHolderEmail               = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_CARD_HOLDER_EMAIL, null, MM_WPFS_ModelConstants::SANITATION_TYPE_EMAIL );
		$this->cardHolderPhone               = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_CARD_HOLDER_PHONE, null );
		$this->customInputValues             = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_CUSTOM_INPUT );
		$this->sameBillingAndShippingAddress = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_SAME_BILLING_AND_SHIPPING_ADDRESS, 0 );
		$this->billingName                   = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_BILLING_NAME );
		$this->billingAddressLine1           = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_BILLING_ADDRESS_LINE_1, MM_WPFS_Binder::EMPTY_STR );
		$this->billingAddressLine2           = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_BILLING_ADDRESS_LINE_2, MM_WPFS_Binder::EMPTY_STR );
		$this->billingAddressCity            = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_BILLING_ADDRESS_CITY, MM_WPFS_Binder::EMPTY_STR );
		$this->billingAddressState           = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_BILLING_ADDRESS_STATE, MM_WPFS_Binder::EMPTY_STR );
		$this->billingAddressZip             = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_BILLING_ADDRESS_ZIP, MM_WPFS_Binder::EMPTY_STR );
		$this->billingAddressCountry         = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_BILLING_ADDRESS_COUNTRY, MM_WPFS_Binder::EMPTY_STR );
		$this->shippingName                  = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_SHIPPING_NAME );
		$this->shippingAddressLine1          = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_SHIPPING_ADDRESS_LINE_1 );
		$this->shippingAddressLine2          = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_SHIPPING_ADDRESS_LINE_2 );
		$this->shippingAddressCity           = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_SHIPPING_ADDRESS_CITY );
		$this->shippingAddressState          = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_SHIPPING_ADDRESS_STATE );
		$this->shippingAddressZip            = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_SHIPPING_ADDRESS_ZIP );
		$this->shippingAddressCountry        = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_SHIPPING_ADDRESS_COUNTRY );
		$this->termsOfUseAccepted            = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_TERMS_OF_USE_ACCEPTED, 0 );
		$this->googleReCaptchaResponse       = $this->getSanitizedArrayParam( $postData, self::PARAM_GOOGLE_RECAPTCHA_RESPONSE );
		$this->nonce                         = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_NONCE );

		/*
				if ( isset( $this->cardHolderName ) && ! empty( $this->cardHolderName ) ) {
					if ( ! isset( $this->billingName ) || empty( $this->billingName ) ) {
						$this->billingName = $this->cardHolderName;
					}
					if ( ! isset( $this->shippingName ) || empty( $this->shippingName ) ) {
						$this->shippingName = $this->cardHolderName;
					}
				} else {
					if ( isset( $this->billingName ) && ! empty( $this->billingName ) ) {
						$this->cardHolderName = $this->billingName;
					} elseif ( isset( $this->shippingName ) && ! empty( $this->shippingName ) ) {
						$this->cardHolderName = $this->shippingName;
					}
				}
		*/

		$this->initBillingAddressCountryComposite();
		$this->initShippingAddressCountryComposite();

		return $bindingResult;
	}

	private function initBillingAddressCountryComposite() {
		if ( isset( $this->billingAddressCountry ) ) {
			$this->__billingAddressCountryComposite = MM_WPFS_Countries::get_country_by_code( $this->billingAddressCountry );
			if ( isset( $this->__billingAddressCountryComposite ) ) {
				$this->__billingAddressCountryName = $this->__billingAddressCountryComposite['name'];
				$this->__billingAddressCountryCode = $this->__billingAddressCountryComposite['alpha-2'];
			}
		}
	}

	private function initShippingAddressCountryComposite() {
		if ( isset( $this->shippingAddressCountry ) ) {
			$this->__shippingAddressCountryComposite = MM_WPFS_Countries::get_country_by_code( $this->shippingAddressCountry );
			if ( isset( $this->__shippingAddressCountryComposite ) ) {
				$this->__shippingAddressCountryName = $this->__shippingAddressCountryComposite['name'];
				$this->__shippingAddressCountryCode = $this->__shippingAddressCountryComposite['alpha-2'];
			}
		}
	}

	/**
	 * @param array $stripeAddressHash
	 */
	public function updateBillingAddressByStripeAddressHash( $stripeAddressHash ) {
		if ( isset( $stripeAddressHash ) ) {
			if ( isset( $stripeAddressHash->line1 ) ) {
				$this->billingAddressLine1 = $stripeAddressHash->line1;
			}
			if ( isset( $stripeAddressHash->line2 ) ) {
				$this->billingAddressLine2 = $stripeAddressHash->line2;
			}
			if ( isset( $stripeAddressHash->city ) ) {
				$this->billingAddressCity = $stripeAddressHash->city;
			}
			if ( isset( $stripeAddressHash->state ) ) {
				$this->billingAddressState = $stripeAddressHash->state;
			}
			if ( isset( $stripeAddressHash->postal_code ) ) {
				$this->billingAddressZip = $stripeAddressHash->postal_code;
			}
			if ( isset( $stripeAddressHash->country ) ) {
				$this->billingAddressCountry = $stripeAddressHash->country;
			}
			$this->initBillingAddressCountryComposite();
		}
	}

	/**
	 * @return MM_WPFS_Database
	 */
	public function getDAO() {
		return $this->__dao;
	}

	public function getData() {
		// tnagy unsupported operation
		return array();
	}

	public function getPostData() {

		$array = array(
			self::PARAM_WPFS_FORM_ACTION              => $this->action,
			self::PARAM_WPFS_FORM_NAME                => $this->formName,
			self::PARAM_WPFS_FORM_GET_PARAMETERS      => $this->formGetParameters,
			self::PARAM_WPFS_REFERRER                 => $this->referrer,
			self::PARAM_WPFS_STRIPE_TOKEN             => $this->stripeToken,
			self::PARAM_WPFS_STRIPE_PAYMENT_METHOD_ID => $this->stripePaymentMethodId,
			self::PARAM_WPFS_STRIPE_PAYMENT_INTENT_ID => $this->stripePaymentIntentId,
			self::PARAM_WPFS_STRIPE_SETUP_INTENT_ID   => $this->stripeSetupIntentId,
			self::PARAM_WPFS_CARD_HOLDER_NAME         => $this->cardHolderName,
			self::PARAM_WPFS_CARD_HOLDER_EMAIL        => $this->cardHolderEmail,
			self::PARAM_WPFS_CARD_HOLDER_PHONE        => $this->cardHolderPhone,
			self::PARAM_WPFS_CUSTOM_INPUT             => $this->customInputValues,
			self::PARAM_WPFS_BILLING_NAME             => $this->billingName,
			self::PARAM_WPFS_BILLING_ADDRESS_LINE_1   => $this->billingAddressLine1,
			self::PARAM_WPFS_BILLING_ADDRESS_LINE_2   => $this->billingAddressLine2,
			self::PARAM_WPFS_BILLING_ADDRESS_CITY     => $this->billingAddressCity,
			self::PARAM_WPFS_BILLING_ADDRESS_STATE    => $this->billingAddressState,
			self::PARAM_WPFS_BILLING_ADDRESS_ZIP      => $this->billingAddressZip,
			self::PARAM_WPFS_BILLING_ADDRESS_COUNTRY  => $this->billingAddressCountry,
			self::PARAM_WPFS_SHIPPING_NAME            => $this->shippingName,
			self::PARAM_WPFS_SHIPPING_ADDRESS_LINE_1  => $this->shippingAddressLine1,
			self::PARAM_WPFS_SHIPPING_ADDRESS_LINE_2  => $this->shippingAddressLine2,
			self::PARAM_WPFS_SHIPPING_ADDRESS_CITY    => $this->shippingAddressCity,
			self::PARAM_WPFS_SHIPPING_ADDRESS_STATE   => $this->shippingAddressState,
			self::PARAM_WPFS_SHIPPING_ADDRESS_ZIP     => $this->shippingAddressZip,
			self::PARAM_WPFS_SHIPPING_ADDRESS_COUNTRY => $this->shippingAddressCountry,
			self::PARAM_WPFS_TERMS_OF_USE_ACCEPTED    => $this->termsOfUseAccepted,
			self::PARAM_GOOGLE_RECAPTCHA_RESPONSE     => $this->googleReCaptchaResponse,
			self::PARAM_WPFS_NONCE                    => $this->nonce
		);

		return $array;
	}

	/**
	 * @return mixed
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * @return string
	 */
	public function getFormGetParameters() {
		return $this->formGetParameters;
	}

	/**
	 * @return mixed
	 */
	public function getReferrer() {
		return $this->referrer;
	}

	/**
	 * @deprecated
	 * @return mixed
	 */
	public function getStripeToken() {
		return $this->stripeToken;
	}

	/**
	 * @return mixed
	 */
	public function getStripePaymentMethodId() {
		return $this->stripePaymentMethodId;
	}

	/**
	 * @return mixed
	 */
	public function getStripePaymentIntentId() {
		return $this->stripePaymentIntentId;
	}

	/**
	 * @return mixed
	 */
	public function getStripeSetupIntentId() {
		return $this->stripeSetupIntentId;
	}

	/**
	 * @return mixed
	 */
	public function getCardHolderName() {
		return $this->cardHolderName;
	}

	/**
	 * @param mixed $cardHolderName
	 */
	public function setCardHolderName( $cardHolderName ) {
		$this->cardHolderName = $cardHolderName;
	}

	/**
	 * @return mixed
	 */
	public function getCardHolderEmail() {
		return $this->cardHolderEmail;
	}

	/**
	 * @param mixed $cardHolderEmail
	 */
	public function setCardHolderEmail( $cardHolderEmail ) {
		$this->cardHolderEmail = $cardHolderEmail;
	}

	/**
	 * @return mixed
	 */
	public function getCardHolderPhone() {
		return $this->cardHolderPhone;
	}

	/**
	 * @param mixed $cardHolderPhone
	 */
	public function setCardHolderPhone( $cardHolderPhone ) {
		$this->cardHolderPhone = $cardHolderPhone;
	}

	/**
	 * @return mixed
	 */
	public function getCustomInputvalues() {
		return $this->customInputValues;
	}

	/**
	 * @return mixed
	 */
	public function getBillingAddressLine1() {
		return $this->billingAddressLine1;
	}

	/**
	 * @return mixed
	 */
	public function getBillingAddressLine2() {
		return $this->billingAddressLine2;
	}

	/**
	 * @return mixed
	 */
	public function getBillingAddressCity() {
		return $this->billingAddressCity;
	}

	/**
	 * @return mixed
	 */
	public function getBillingAddressState() {
		return $this->billingAddressState;
	}

	/**
	 * @return mixed
	 */
	public function getBillingAddressZip() {
		return $this->billingAddressZip;
	}

	/**
	 * @return mixed
	 */
	public function getBillingAddressCountry() {
		return $this->billingAddressCountry;
	}

	/**
	 * @return mixed
	 */
	public function getShippingAddressLine1() {
		return $this->shippingAddressLine1;
	}

	/**
	 * @return mixed
	 */
	public function getShippingAddressLine2() {
		return $this->shippingAddressLine2;
	}

	/**
	 * @return mixed
	 */
	public function getShippingAddressCity() {
		return $this->shippingAddressCity;
	}

	/**
	 * @return mixed
	 */
	public function getShippingAddressState() {
		return $this->shippingAddressState;
	}

	/**
	 * @return mixed
	 */
	public function getShippingAddressZip() {
		return $this->shippingAddressZip;
	}

	/**
	 * @return mixed
	 */
	public function getShippingAddressCountry() {
		return $this->shippingAddressCountry;
	}

	/**
	 * @return mixed
	 */
	public function getTermsOfUseAccepted() {
		return $this->termsOfUseAccepted;
	}

	/**
	 * @return mixed
	 */
	public function getGoogleReCaptchaResponse() {
		return $this->googleReCaptchaResponse;
	}

	/**
	 * @return mixed
	 */
	public function getTransactionId() {
		return $this->transactionId;
	}

	/**
	 * @param mixed $transactionId
	 */
	public function setTransactionId( $transactionId ) {
		$this->transactionId = $transactionId;
	}

	/**
	 * @return mixed
	 */
	public function getFormHash() {
		return $this->__formHash;
	}

	/**
	 * @param mixed $formHash
	 */
	public function setFormHash( $formHash ) {
		$this->__formHash = $formHash;
	}

	/**
	 * @return mixed
	 */
	public function getNonce() {
		return $this->nonce;
	}

	/**
	 * @param mixed $nonce
	 */
	public function setNonce( $nonce ) {
		$this->nonce = $nonce;
	}

	/**
	 * @return mixed
	 */
	public function getBillingAddressCountryComposite() {
		return $this->__billingAddressCountryComposite;
	}

	/**
	 * @param mixed $billing_address_country_composite
	 */
	public function setBillingAddressCountryComposite( $billing_address_country_composite ) {
		$this->__billingAddressCountryComposite = $billing_address_country_composite;
	}

	/**
	 * @return mixed
	 */
	public function getBillingAddressCountryName() {
		return $this->__billingAddressCountryName;
	}

	/**
	 * @param mixed $billing_address_country_name
	 */
	public function setBillingAddressCountryName( $billing_address_country_name ) {
		$this->__billingAddressCountryName = $billing_address_country_name;
	}

	/**
	 * @return mixed
	 */
	public function getShippingAddressCountryComposite() {
		return $this->__shippingAddressCountryComposite;
	}

	/**
	 * @return mixed
	 */
	public function getShippingAddressCountryName() {
		return $this->__shippingAddressCountryName;
	}

	/**
	 * @return array
	 */
	public function getMetadata() {
		$metadata = array();

		if ( isset( $this->cardHolderEmail ) ) {
			$metadata['customer_email'] = $this->cardHolderEmail;
		}
		if ( isset( $this->cardHolderName ) ) {
			$metadata['customer_name'] = $this->cardHolderName;
		}
		if ( isset( $this->formName ) ) {
			$metadata['form_name'] = $this->formName;
		}
		if ( isset( $this->__form->allowMultipleSubscriptions ) ) {
			$metadata['allow_multiple_subscriptions'] = $this->__form->allowMultipleSubscriptions;
			if ( isset( $this->__form->allowMultipleSubscriptions ) && 1 == $this->__form->allowMultipleSubscriptions ) {
				$metadata['maximum_quantity_of_subscriptions'] = $this->__form->maximumQuantityOfSubscriptions;
			}
		}
		if (
			( isset( $this->__form->showAddress ) && 1 == $this->__form->showAddress )
			|| ( isset( $this->__form->showBillingAddress ) && 1 == $this->__form->showBillingAddress )
		) {
			if ( isset( $this->billingName ) ) {
				$metadata['billing_name'] = $this->billingName;
			}
			if ( isset( $this->billingAddressLine1 ) || isset( $this->billingAddressZip ) || isset( $this->billingAddressCity ) || isset( $this->billingAddressCountry ) ) {
				$metadata['billing_address'] = implode( '|', array(
					$this->billingAddressLine1,
					$this->billingAddressLine2,
					$this->billingAddressZip,
					$this->billingAddressCity,
					$this->billingAddressState,
					$this->__billingAddressCountryName,
					$this->__billingAddressCountryCode
				) );
			}
		}
		if ( isset( $this->__form->showShippingAddress ) && 1 == $this->__form->showShippingAddress ) {
			if ( isset( $this->shippingName ) ) {
				$metadata['shipping_name'] = $this->shippingName;
			}
			if ( isset( $this->shippingAddressLine1 ) || isset( $this->shippingAddressZip ) || isset( $this->shippingAddressCity ) || isset( $this->shippingAddressCountry ) ) {
				$metadata['shipping_address'] = implode( '|', array(
					$this->shippingAddressLine1,
					$this->shippingAddressLine2,
					$this->shippingAddressZip,
					$this->shippingAddressCity,
					$this->shippingAddressState,
					$this->__shippingAddressCountryName,
					$this->__shippingAddressCountryCode
				) );
			}
		}
		if ( is_null( $this->__form->customInputs ) ) {
			$customInputValueString = is_array( $this->customInputValues ) ? implode( ",", $this->customInputValues ) : printf( $this->customInputValues );
			if ( ! empty( $customInputValueString ) ) {
				$metadata['custom_inputs'] = $customInputValueString;
			}
		} else {
			$customInputLabels = $this->getDecodedCustomInputLabels();
			foreach ( $customInputLabels as $i => $label ) {
				$key = $label;
				if ( array_key_exists( $key, $metadata ) ) {
					$key = $label . $i;
				}
				if ( ! empty( $this->customInputValues[ $i ] ) ) {
					$metadata[ $key ] = $this->customInputValues[ $i ];
				}
			}
		}

		// users can add custom metadata via filter
		try {
			$user_meta = apply_filters( MM_WPFS::FILTER_NAME_ADD_TRANSACTION_METADATA, array(), $this->getFormName(), $this->getFormGetParametersAsArray() );
			$metadata  = array_merge( $metadata, $user_meta );
		} catch ( Exception $ex ) {
			MM_WPFS_Utils::logException( $ex, $this );
		}

		return $metadata;
	}

	/**
	 * @return array
	 */
	public function getDecodedCustomInputLabels() {
		$customInputLabels = array();
		if ( isset( $this->__form->customInputs ) ) {
			$customInputLabels = explode( '{{', $this->__form->customInputs );
		}

		return $customInputLabels;
	}

	/**
	 * @return mixed
	 */
	public function getFormName() {
		return $this->formName;
	}

	/**
	 * @return array
	 */
	public function getFormGetParametersAsArray() {
		$res = json_decode( $this->formGetParameters, true );

		return $res ? $res : array();
	}

	/**
	 * @return \StripeWPFS\Customer
	 */
	public function getStripeCustomer() {
		return $this->__stripeCustomer;
	}

	/**
	 * @param \StripeWPFS\Customer $stripeCustomer
	 * @param bool $updatePropertiesByCustomer
	 */
	public function setStripeCustomer( $stripeCustomer, $updatePropertiesByCustomer = false ) {
		$this->__stripeCustomer = $stripeCustomer;
		if ( $updatePropertiesByCustomer && ! is_null( $stripeCustomer ) ) {
			$this->cardHolderEmail = $stripeCustomer->email;
			$this->cardHolderName  = $stripeCustomer->name;
			$this->cardHolderPhone = $stripeCustomer->phone;
			$this->billingName     = ! is_null( $this->cardHolderName ) ? $this->cardHolderName : null;
			if ( isset( $stripeCustomer->address ) ) {
				$this->billingAddressLine1   = $stripeCustomer->address->line1;
				$this->billingAddressLine2   = $stripeCustomer->address->line2;
				$this->billingAddressCity    = $stripeCustomer->address->city;
				$this->billingAddressState   = $stripeCustomer->address->state;
				$this->billingAddressZip     = $stripeCustomer->address->postal_code;
				$this->billingAddressCountry = $stripeCustomer->address->country;
				$this->initBillingAddressCountryComposite();
			}
			if ( isset( $stripeCustomer->shipping ) && isset( $stripeCustomer->shipping->address ) ) {
				$this->shippingName           = $stripeCustomer->shipping->name;
				$this->shippingAddressLine1   = $stripeCustomer->shipping->address->line1;
				$this->shippingAddressLine2   = $stripeCustomer->shipping->address->line2;
				$this->shippingAddressCity    = $stripeCustomer->shipping->address->city;
				$this->shippingAddressState   = $stripeCustomer->shipping->address->state;
				$this->shippingAddressZip     = $stripeCustomer->shipping->address->postal_code;
				$this->shippingAddressCountry = $stripeCustomer->shipping->address->country;
				$this->initShippingAddressCountryComposite();
			}
		}
	}

	/**
	 * @return \StripeWPFS\PaymentMethod
	 */
	public function getStripePaymentMethod() {
		return $this->__stripePaymentMethod;
	}

	/**
	 * @param \StripeWPFS\PaymentMethod $stripePaymentMethod
	 */
	public function setStripePaymentMethod( $stripePaymentMethod ) {
		$this->__stripePaymentMethod = $stripePaymentMethod;
	}

	/**
	 * @return mixed
	 */
	public function getSameBillingAndShippingAddress() {
		return $this->sameBillingAndShippingAddress;
	}

	/**
	 * @return mixed
	 */
	public function getBillingName() {
		return $this->billingName;
	}

	/**
	 * @param mixed $billingName
	 */
	public function setBillingName( $billingName ) {
		$this->billingName = $billingName;
	}

	/**
	 * @return mixed
	 */
	public function getShippingName() {
		return $this->shippingName;
	}

	/**
	 * @param mixed $shippingName
	 */
	public function setShippingName( $shippingName ) {
		$this->shippingName = $shippingName;
	}

	/**
	 * @param bool $mayReturnNull
	 *
	 * @return array
	 */
	public function getBillingAddress( $mayReturnNull = true ) {
		return $this->getAddressArray(
			$mayReturnNull,
			$this->billingAddressLine1,
			$this->billingAddressLine2,
			$this->billingAddressCity,
			$this->billingAddressState,
			$this->__billingAddressCountryName,
			$this->__billingAddressCountryCode,
			$this->billingAddressZip
		);
	}

	/**
	 * @param $mayReturnNull
	 * @param $line1
	 * @param $line2
	 * @param $city
	 * @param $state
	 * @param $countryName
	 * @param $countryCode
	 * @param $zip
	 *
	 * @return array
	 */
	protected function getAddressArray( $mayReturnNull, $line1, $line2, $city, $state, $countryName, $countryCode, $zip ) {
		$addressData = array(
			self::ARRAY_KEY_ADDRESS_LINE_1       => is_null( $line1 ) ? '' : $line1,
			self::ARRAY_KEY_ADDRESS_LINE_2       => is_null( $line2 ) ? '' : $line2,
			self::ARRAY_KEY_ADDRESS_CITY         => is_null( $city ) ? '' : $city,
			self::ARRAY_KEY_ADDRESS_STATE        => is_null( $state ) ? '' : $state,
			self::ARRAY_KEY_ADDRESS_COUNTRY      => is_null( $countryName ) ? '' : $countryName,
			self::ARRAY_KEY_ADDRESS_COUNTRY_CODE => is_null( $countryCode ) ? '' : $countryCode,
			self::ARRAY_KEY_ADDRESS_ZIP          => is_null( $zip ) ? '' : $zip
		);
		if ( $mayReturnNull ) {
			$hasNotEmptyValue = false;
			foreach ( $addressData as $key => $value ) {
				if ( $value !== '' ) {
					$hasNotEmptyValue = true;
				}
			}
			if ( $hasNotEmptyValue ) {
				return $addressData;
			} else {
				return null;
			}
		}

		return $addressData;
	}

	/**
	 * @param bool $mayReturnNull
	 *
	 * @return array
	 */
	public function getShippingAddress( $mayReturnNull = true ) {
		return $this->getAddressArray(
			$mayReturnNull,
			$this->shippingAddressLine1,
			$this->shippingAddressLine2,
			$this->shippingAddressCity,
			$this->shippingAddressState,
			$this->__shippingAddressCountryName,
			$this->__shippingAddressCountryCode,
			$this->shippingAddressZip
		);
	}

	/**
	 * @return mixed
	 */
	public function getProductName() {
		return $this->__productName;
	}

	/**
	 * @param mixed $productName
	 */
	public function setProductName( $productName ) {
		$this->__productName = $productName;
	}

	/**
	 * @param $popupFormSubmit
	 *
	 * @return array|mixed|object
	 */
	public function extractFormModelDataFromPopupFormSubmit( $popupFormSubmit ) {
		$postData = array();
		if ( isset( $popupFormSubmit ) && isset( $popupFormSubmit->postData ) ) {
			$postData = json_decode(
				$popupFormSubmit->postData,
				/* to associative array */
				true
			);
			if ( JSON_ERROR_NONE !== json_last_error() ) {
				$postData = array();
			}
		}

		return $postData;
	}

	/**
	 * @param \StripeWPFS\Checkout\Session $checkoutSession
	 *
	 * @return array
	 */
	public function extractFormModelDataFromCheckoutSession( $checkoutSession ) {
		if ( $this->debugLog ) {
			MM_WPFS_Utils::log( 'extractFormModelDataFromCheckoutSession(): CALLED, $checkoutSession=' . print_r( $checkoutSession, true ) );
		}
		// todo tnagy extract data from setup intent / subscription's setup intent / payment intent
		$result = array();

		if ( $checkoutSession instanceof \StripeWPFS\Checkout\Session && isset( $checkoutSession->payment_intent ) ) {
			if ( $checkoutSession->payment_intent instanceof \StripeWPFS\PaymentIntent ) {
				$paymentIntent = $checkoutSession->payment_intent;
			} else {
				$paymentIntent = \StripeWPFS\PaymentIntent::retrieve( $checkoutSession->payment_intent );
			}
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'extractFormModelDataFromCheckoutSession(): paymentIntent=' . print_r( $paymentIntent, true ) );
			}
			if ( $paymentIntent instanceof \StripeWPFS\PaymentIntent && isset( $paymentIntent->payment_method ) ) {
				if ( $paymentIntent->payment_method instanceof \StripeWPFS\PaymentMethod ) {
					$paymentMethod = $paymentIntent->payment_method;
				} else {
					$paymentMethod = \StripeWPFS\PaymentMethod::retrieve( $paymentIntent->payment_method );
				}
				if ( $this->debugLog ) {
					MM_WPFS_Utils::log( 'extractFormModelDataFromCheckoutSession(): paymentMethod=' . print_r( $paymentMethod, true ) );
				}
				if ( $paymentMethod instanceof \StripeWPFS\PaymentMethod
				     && isset( $paymentMethod->billing_details )
				     && isset( $paymentMethod->billing_details->address )
				) {
					$result[ MM_WPFS_Public_FormModel::PARAM_WPFS_BILLING_NAME ]            = $paymentMethod->billing_details->name;
					$result[ MM_WPFS_Public_FormModel::PARAM_WPFS_BILLING_ADDRESS_LINE_1 ]  = $paymentMethod->billing_details->address->line1;
					$result[ MM_WPFS_Public_FormModel::PARAM_WPFS_BILLING_ADDRESS_LINE_2 ]  = $paymentMethod->billing_details->address->line2;
					$result[ MM_WPFS_Public_FormModel::PARAM_WPFS_BILLING_ADDRESS_CITY ]    = $paymentMethod->billing_details->address->city;
					$result[ MM_WPFS_Public_FormModel::PARAM_WPFS_BILLING_ADDRESS_STATE ]   = $paymentMethod->billing_details->address->state;
					$result[ MM_WPFS_Public_FormModel::PARAM_WPFS_BILLING_ADDRESS_ZIP ]     = $paymentMethod->billing_details->address->postal_code;
					$result[ MM_WPFS_Public_FormModel::PARAM_WPFS_BILLING_ADDRESS_COUNTRY ] = $paymentMethod->billing_details->address->country;
				}
			}
		}
		$result[ MM_WPFS_Public_FormModel::PARAM_WPFS_SHIPPING_NAME ]            = null;
		$result[ MM_WPFS_Public_FormModel::PARAM_WPFS_SHIPPING_ADDRESS_LINE_1 ]  = null;
		$result[ MM_WPFS_Public_FormModel::PARAM_WPFS_SHIPPING_ADDRESS_LINE_2 ]  = null;
		$result[ MM_WPFS_Public_FormModel::PARAM_WPFS_SHIPPING_ADDRESS_CITY ]    = null;
		$result[ MM_WPFS_Public_FormModel::PARAM_WPFS_SHIPPING_ADDRESS_STATE ]   = null;
		$result[ MM_WPFS_Public_FormModel::PARAM_WPFS_SHIPPING_ADDRESS_COUNTRY ] = null;
		$result[ MM_WPFS_Public_FormModel::PARAM_WPFS_SHIPPING_ADDRESS_ZIP ]     = null;

		return $result;
	}

	public function afterBind() {
		$this->updateShippingAddress();
	}

	private function updateShippingAddress() {
		if ( ! is_null( $this->getForm() ) ) {
			if ( 1 == $this->sameBillingAndShippingAddress && 1 == $this->getForm()->showShippingAddress ) {
				$this->shippingName           = $this->billingName;
				$this->shippingAddressLine1   = $this->billingAddressLine1;
				$this->shippingAddressLine2   = $this->billingAddressLine2;
				$this->shippingAddressCity    = $this->billingAddressCity;
				$this->shippingAddressState   = $this->billingAddressState;
				$this->shippingAddressZip     = $this->billingAddressZip;
				$this->shippingAddressCountry = $this->billingAddressCountry;
				$this->initShippingAddressCountryComposite();
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function getForm() {
		return $this->__form;
	}

	/**
	 * @param mixed $form
	 */
	public function setForm( $form ) {
		$this->__form = $form;
		$this->prepareFormHash();
	}

	protected function prepareFormHash() {
		$formType = MM_WPFS_Utils::getFormType( $this->__form );
		$formId   = MM_WPFS_Utils::getFormId( $this->__form );
		$formName = $this->__form->name;
		$this->setFormHash(
			esc_attr(
				MM_WPFS_Utils::generate_form_hash(
					$formType,
					$formId,
					$formName
				)
			)
		);
	}

	/**
	 * @deprecated
	 *
	 * @param $stripeCustomer
	 *
	 * @return null
	 */
	protected function retrieveStripeCustomerName( $stripeCustomer ) {
		$customerName = null;
		if ( isset( $stripeCustomer ) && isset( $stripeCustomer->metadata ) && isset( $stripeCustomer->metadata->customer_name ) ) {
			$customerName = $stripeCustomer->metadata->customer_name;
		}
		if ( is_null( $customerName ) ) {
			if ( isset( $stripeCustomer->subscriptions ) ) {
				foreach ( $stripeCustomer->subscriptions as $subscription ) {
					if ( is_null( $customerName ) ) {
						if ( isset( $subscription->metadata ) && isset( $subscription->metadata->customer_name ) ) {
							$customerName = $subscription->metadata->customer_name;
						}
					}
				}
			}
		}

		return $customerName;
	}

}

abstract class MM_WPFS_Public_PaymentFormModel extends MM_WPFS_Public_FormModel {

	const PARAM_WPFS_CUSTOM_AMOUNT_INDEX = 'wpfs-custom-amount-index';
	const PARAM_WPFS_CUSTOM_AMOUNT = 'wpfs-custom-amount';
	const PARAM_WPFS_CUSTOM_AMOUNT_UNIQUE = 'wpfs-custom-amount-unique';
	const CUSTOM_AMOUNT_LABEL_MACRO_AMOUNT = '{amount}';
	const INITIAL_CUSTOM_AMOUNT_INDEX = - 1;
	protected $customAmountIndex;
	protected $customAmountValue;
	protected $customAmountUniqueValue;
	protected $__amount;

	/**
	 * MM_WPFS_Public_PaymentFormModel constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	public function bindByArray( $postData ) {
		$bindingResult                 = parent::bindByArray( $postData );
		$this->customAmountIndex       = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_CUSTOM_AMOUNT_INDEX, self::INITIAL_CUSTOM_AMOUNT_INDEX );
		$this->customAmountValue       = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_CUSTOM_AMOUNT );
		$this->customAmountUniqueValue = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_CUSTOM_AMOUNT_UNIQUE );

		if ( isset( $this->__validator ) ) {
			$this->__validator->validate( $bindingResult, $this );
		}

		$this->afterBind();

		return $bindingResult;
	}

	public function getPostData() {
		$parentPostData = parent::getPostData();

		$postData = array(
			self::PARAM_WPFS_CUSTOM_AMOUNT_INDEX  => $this->customAmountIndex,
			self::PARAM_WPFS_CUSTOM_AMOUNT        => $this->customAmountValue,
			self::PARAM_WPFS_CUSTOM_AMOUNT_UNIQUE => $this->customAmountUniqueValue
		);

		return array_merge( $postData, $parentPostData );
	}

	/**
	 * @return mixed
	 */
	public function getCustomAmountIndex() {
		return $this->customAmountIndex;
	}

	/**
	 * @param mixed $customAmountIndex
	 */
	public function setCustomAmountIndex( $customAmountIndex ) {
		$this->customAmountIndex = $customAmountIndex;
	}

	/**
	 * @return mixed
	 */
	public function getCustomAmountValue() {
		return $this->customAmountValue;
	}

	/**
	 * @param mixed $customAmountValue
	 */
	public function setCustomAmountValue( $customAmountValue ) {
		$this->customAmountValue = $customAmountValue;
	}

	/**
	 * @return mixed
	 */
	public function getCustomAmountUniqueValue() {
		return $this->customAmountUniqueValue;
	}

	/**
	 * @param mixed $customAmountUniqueValue
	 */
	public function setCustomAmountUniqueValue( $customAmountUniqueValue ) {
		$this->customAmountUniqueValue = $customAmountUniqueValue;
	}

	/**
	 * @return mixed
	 */
	public function getAmount() {
		return $this->__amount;
	}

	/**
	 * @param mixed $amount
	 */
	public function setAmount( $amount ) {
		$this->__amount = $amount;
	}

	public function setForm( $form ) {
		parent::setForm( $form );
		$this->prepareAmountAndProductName();
	}

	protected function prepareAmountAndProductName() {
		$this->__amount = null;
		if ( isset( $this->__form->productDesc ) ) {
			$this->__productName = esc_attr( $this->__form->productDesc );
		} else {
			$this->__productName = esc_attr( MM_WPFS_Admin_PopupFormModel::getDefaultProductDescription() );
		}
		if ( MM_WPFS::PAYMENT_TYPE_SPECIFIED_AMOUNT === $this->__form->customAmount ) {
			$this->__amount = $this->__form->amount;
		} elseif ( MM_WPFS::PAYMENT_TYPE_LIST_OF_AMOUNTS === $this->__form->customAmount ) {
			if ( 1 == $this->__form->allowListOfAmountsCustom && 'other' === $this->customAmountValue ) {
				$parsedAmount   = MM_WPFS_Currencies::parseByForm( $this->__form, $this->__form->currency, $this->customAmountUniqueValue );
				$this->__amount = MM_WPFS_Utils::parse_amount( $this->__form->currency, $parsedAmount );
			} else {
				$listOfAmounts = json_decode( $this->__form->listOfAmounts );
				if ( isset( $this->customAmountIndex ) && $this->customAmountIndex > self::INITIAL_CUSTOM_AMOUNT_INDEX && count( $listOfAmounts ) > $this->customAmountIndex ) {
					$customAmountElement                 = $listOfAmounts[ $this->customAmountIndex ];
					$customAmountAmount                  = $customAmountElement[0];
					$customAmountElementDescription      = $customAmountElement[1];
					$customAmountElementAmountLabel      = MM_WPFS_Currencies::formatAndEscapeByForm( $this->__form, $this->__form->currency, $customAmountAmount );
					$customAmountElementDescriptionLabel = MM_WPFS_Localization::translateLabel( $customAmountElementDescription );
					if ( strpos( $customAmountElementDescription, self::CUSTOM_AMOUNT_LABEL_MACRO_AMOUNT ) !== false ) {
						$customAmountElementDescriptionLabel = str_replace( self::CUSTOM_AMOUNT_LABEL_MACRO_AMOUNT, $customAmountElementAmountLabel, $customAmountElementDescriptionLabel );
					}
					$this->__amount      = $customAmountAmount;
					$this->__productName = $customAmountElementDescriptionLabel;
				}
			}
		} elseif ( MM_WPFS::PAYMENT_TYPE_CUSTOM_AMOUNT == $this->__form->customAmount ) {
			$parsedAmount   = MM_WPFS_Currencies::parseByForm( $this->__form, $this->__form->currency, $this->customAmountUniqueValue );
			$this->__amount = MM_WPFS_Utils::parse_amount( $this->__form->currency, $parsedAmount );
		}
	}

}

abstract class MM_WPFS_Public_DonationFormModel extends MM_WPFS_Public_FormModel {

    const PARAM_WPFS_CUSTOM_AMOUNT_INDEX = 'wpfs-custom-amount-index';
    const PARAM_WPFS_CUSTOM_AMOUNT = 'wpfs-custom-amount';
    const PARAM_WPFS_CUSTOM_AMOUNT_UNIQUE = 'wpfs-custom-amount-unique';
    const PARAM_WPFS_DONATION_FREQUENCY = 'wpfs-donation-frequency';
    const INITIAL_CUSTOM_AMOUNT_INDEX = - 1;
    protected $customAmountIndex;
    protected $customAmountValue;
    protected $customAmountUniqueValue;
    protected $donationFrequency;
    protected $__amount;

    /**
     * MM_WPFS_Public_DonationFormModel constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    public function bindByArray( $postData ) {
        $bindingResult                 = parent::bindByArray( $postData );
        $this->customAmountIndex       = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_CUSTOM_AMOUNT_INDEX, self::INITIAL_CUSTOM_AMOUNT_INDEX );
        $this->customAmountValue       = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_CUSTOM_AMOUNT );
        $this->customAmountUniqueValue = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_CUSTOM_AMOUNT_UNIQUE );
        $this->donationFrequency       = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_DONATION_FREQUENCY );

        if ( isset( $this->__validator ) ) {
            $this->__validator->validate( $bindingResult, $this );
        }

        $this->afterBind();

        return $bindingResult;
    }

    public function getPostData() {
        $parentPostData = parent::getPostData();

        $postData = array(
            self::PARAM_WPFS_CUSTOM_AMOUNT_INDEX  => $this->customAmountIndex,
            self::PARAM_WPFS_CUSTOM_AMOUNT        => $this->customAmountValue,
            self::PARAM_WPFS_CUSTOM_AMOUNT_UNIQUE => $this->customAmountUniqueValue,
            self::PARAM_WPFS_DONATION_FREQUENCY   => $this->donationFrequency
        );

        return array_merge( $postData, $parentPostData );
    }

    /**
     * @return mixed
     */
    public function getCustomAmountIndex() {
        return $this->customAmountIndex;
    }

    /**
     * @param mixed $customAmountIndex
     */
    public function setCustomAmountIndex( $customAmountIndex ) {
        $this->customAmountIndex = $customAmountIndex;
    }

    /**
     * @return mixed
     */
    public function getCustomAmountValue() {
        return $this->customAmountValue;
    }

    /**
     * @param mixed $customAmountValue
     */
    public function setCustomAmountValue( $customAmountValue ) {
        $this->customAmountValue = $customAmountValue;
    }

    /**
     * @return mixed
     */
    public function getCustomAmountUniqueValue() {
        return $this->customAmountUniqueValue;
    }

    /**
     * @param mixed $customAmountUniqueValue
     */
    public function setCustomAmountUniqueValue( $customAmountUniqueValue ) {
        $this->customAmountUniqueValue = $customAmountUniqueValue;
    }

    /**
     * @return mixed
     */
    public function getAmount() {
        return $this->__amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount( $amount ) {
        $this->__amount = $amount;
    }

    public function setForm( $form ) {
        parent::setForm( $form );
        $this->prepareAmountAndProductName();
    }

    protected function prepareAmountAndProductName() {
        $this->__amount = null;
        if ( isset( $this->__form->productDesc ) ) {
            $this->__productName = esc_attr( $this->__form->productDesc );
        } else {
            $this->__productName = __("Donation", MM_WPFS::L10N_DOMAIN_PUBLIC );
        }

        if ( 1 == $this->__form->allowCustomDonationAmount && 'other' === $this->customAmountValue ) {
            $parsedAmount   = MM_WPFS_Currencies::parseByForm( $this->__form, $this->__form->currency, $this->customAmountUniqueValue );
            $this->__amount = MM_WPFS_Utils::parse_amount( $this->__form->currency, $parsedAmount );
        } else {
            $donationAmounts = MM_WPFS_Utils::decodeJsonArray( $this->__form->donationAmounts );
            if ( isset( $this->customAmountIndex ) && $this->customAmountIndex > self::INITIAL_CUSTOM_AMOUNT_INDEX && count( $donationAmounts ) > $this->customAmountIndex ) {
                $this->__amount = $donationAmounts[ $this->customAmountIndex ];
            }
        }
    }

    public function getDonationFrequency() {
        return $this->donationFrequency;
    }

    public function isRecurringDonation( ) {
        return $this->donationFrequency !== MM_WPFS_DonationFormViewConstants::FIELD_VALUE_DONATION_FREQUENCY_ONE_TIME ? true : false;
    }

}

class MM_WPFS_Public_InlineDonationFormModel extends MM_WPFS_Public_DonationFormModel implements MM_WPFS_Public_InlineForm {

    /**
     * MM_WPFS_Public_InlineDonationFormModel constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->__validator = new MM_WPFS_InlineDonationFormValidator();
    }

}

class MM_WPFS_Public_PopupDonationFormModel extends MM_WPFS_Public_DonationFormModel implements MM_WPFS_Public_PopupForm {

    /**
     * MM_WPFS_Public_PopupDonationFormModel constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->__validator = new MM_WPFS_PopupDonationFormValidator();
    }

}


abstract class MM_WPFS_Public_SubscriptionFormModel extends MM_WPFS_Public_FormModel {

	const PARAM_WPFS_STRIPE_PLAN = 'wpfs-plan';
	const PARAM_WPFS_STRIPE_PLAN_QUANTITY = 'wpfs-plan-quantity';
	const PARAM_WPFS_COUPON = 'wpfs-coupon';
	const PARAM_WPFS_AMOUNT_WITH_COUPON_APPLIED = 'wpfs-amount-with-coupon-applied';

	protected $__stripe;

	protected $stripePlanId;
	protected $stripePlanQuantity;
	protected $couponCode;
	protected $amountWithCouponApplied;
	/**
	 * @var \StripeWPFS\Plan
	 */
	protected $__stripePlan;
	protected $__stripePlanSetupFee;
	protected $__stripePlanAmount;
	/**
	 * @var \StripeWPFS\Coupon
	 */
	protected $__stripeCoupon;
	protected $__stripeCouponDiscount = 0;

	/**
	 * MM_WPFS_Public_SubscriptionFormModel constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->__stripe = new MM_WPFS_Stripe();
	}

	public function bindByArray( $postData ) {
		$bindingResult                 = parent::bindByArray( $postData );
		$this->stripePlanId            = $this->getHTMLDecodedArrayParam( $postData, self::PARAM_WPFS_STRIPE_PLAN );
		$this->stripePlanQuantity      = $this->getNumericArrayParam( $postData, self::PARAM_WPFS_STRIPE_PLAN_QUANTITY, 1 );
		$this->couponCode              = $this->getSanitizedArrayParam( $postData, self::PARAM_WPFS_COUPON );
		$this->amountWithCouponApplied = $this->getNumericArrayParam( $postData, self::PARAM_WPFS_AMOUNT_WITH_COUPON_APPLIED );
		$this->__productName           = '';

		$this->prepareStripeCouponAndPlan();

		if ( isset( $this->__validator ) ) {
			$this->__validator->validate( $bindingResult, $this );
		}

		$this->afterBind();

		return $bindingResult;
	}

	private function prepareStripeCouponAndPlan() {
		$this->prepareStripeCoupon();
		if ( isset( $this->stripePlanId ) ) {
			$this->__stripePlan         = $this->__stripe->retrieve_plan( $this->stripePlanId );
			$this->__productName        = $this->__stripePlan->product->name;
			$this->__stripePlanSetupFee = MM_WPFS_Utils::get_setup_fee_for_plan( $this->__stripePlan );
			if ( isset( $this->__stripeCoupon ) ) {
				if ( isset( $this->__stripeCoupon->percent_off ) ) {
					$percentOff                   = intval( $this->__stripeCoupon->percent_off ) / 100;
					$this->__stripeCouponDiscount = round( $this->__stripePlan->amount * $percentOff );
				} elseif ( isset( $this->__stripeCoupon->amount_off ) ) {
					if ( $this->__stripePlan->currency === $this->__stripeCoupon->currency ) {
						$this->__stripeCouponDiscount = intval( $this->__stripeCoupon->amount_off );
					}
				}
			}
			$this->__stripePlanAmount = $this->__stripePlan->amount - $this->__stripeCouponDiscount;
		}
	}

	private function prepareStripeCoupon() {
		if ( isset( $this->getForm()->showCouponInput ) && 1 == $this->getForm()->showCouponInput ) {
			if ( isset( $this->couponCode ) && ! empty( $this->couponCode ) ) {
				$this->__stripeCoupon = $this->__stripe->get_coupon( $this->couponCode );
			}
		}
	}

	public function getPostData() {
		$parentPostData = parent::getPostData();

		$postData = array(
			self::PARAM_WPFS_STRIPE_PLAN                => $this->stripePlanId,
			self::PARAM_WPFS_STRIPE_PLAN_QUANTITY       => $this->stripePlanQuantity,
			self::PARAM_WPFS_COUPON                     => $this->couponCode,
			self::PARAM_WPFS_AMOUNT_WITH_COUPON_APPLIED => $this->amountWithCouponApplied
		);

		return array_merge( $postData, $parentPostData );
	}

	/**
	 * @return mixed
	 */
	public function getStripePlanId() {
		return $this->stripePlanId;
	}

	/**
	 * @return mixed
	 */
	public function getStripePlanQuantity() {
		return $this->stripePlanQuantity;
	}

	/**
	 * @return mixed
	 */
	public function getCouponCode() {
		return $this->couponCode;
	}

	/**
	 * @return mixed
	 */
	public function getAmountWithCouponApplied() {
		return $this->amountWithCouponApplied;
	}

	/**
	 * @return mixed
	 */
	public function getStripePlan() {
		return $this->__stripePlan;
	}

	/**
	 * @return mixed
	 */
	public function getStripePlanSetupFee() {
		return $this->__stripePlanSetupFee;
	}

	/**
	 * @return mixed
	 */
	public function getStripePlanAmount() {
		return $this->__stripePlanAmount;
	}

	/**
	 * @return int
	 */
	public function getStripeCouponDiscount() {
		return $this->__stripeCouponDiscount;
	}

	/**
	 * @return mixed
	 */
	public function getStripeCoupon() {
		return $this->__stripeCoupon;
	}

}

class MM_WPFS_Public_InlinePaymentFormModel extends MM_WPFS_Public_PaymentFormModel implements MM_WPFS_Public_InlineForm {

	/**
	 * MM_WPFS_Public_InlinePaymentFormModel constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->__validator = new MM_WPFS_InlinePaymentFormValidator();
	}

}

class MM_WPFS_Public_InlineSubscriptionFormModel extends MM_WPFS_Public_SubscriptionFormModel implements MM_WPFS_Public_InlineForm {

	/**
	 * MM_WPFS_Public_InlineSubscriptionFormModel constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->__validator = new MM_WPFS_InlineSubscriptionFormValidator();
	}

}

class MM_WPFS_Public_PopupPaymentFormModel extends MM_WPFS_Public_PaymentFormModel implements MM_WPFS_Public_PopupForm {

	/**
	 * MM_WPFS_Public_PopupPaymentFormModel constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->__validator = new MM_WPFS_PopupPaymentFormValidator();
	}

}

class MM_WPFS_Public_PopupSubscriptionFormModel extends MM_WPFS_Public_SubscriptionFormModel implements MM_WPFS_Public_PopupForm {

	/**
	 * MM_WPFS_Public_PopupSubscriptionFormModel constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->__validator = new MM_WPFS_PopupSubscriptionFormValidator();
	}

}