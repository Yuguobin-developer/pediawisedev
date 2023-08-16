<?php

/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2019.11.27.
 * Time: 14:42
 */
class MM_WPFS_LoggerService {

	const LEVEL_DEBUG = 'DEBUG';
	const LEVEL_INFO = 'INFO';
	const LEVEL_WARN = 'WARN';
	const LEVEL_ERROR = 'ERROR';

	const LEVEL_PRIORITY_DEBUG = 10;
	const LEVEL_PRIORITY_INFO = 20;
	const LEVEL_PRIORITY_WARN = 30;
	const LEVEL_PRIORITY_ERROR = 40;

	const MODULE_PATCHER = 'PATCHER';
	const MODULE_DATABASE = 'DATABASE';
	const MODULE_ADMIN = 'ADMIN';
	const MODULE_MY_ACCOUNT = 'MY_ACCOUNT';
	const MODULE_WEBHOOK_EVENT_HANDLER = 'WEBHOOK_EVENT_HANDLER';
	const MODULE_CHECKOUT_SUBMISSION = 'CHECKOUT_SUBMISSION';

	/**
	 * @var MM_WPFS_Database
	 */
	private $db;

	/**
	 * MM_WPFS_LoggerService constructor.
	 */
	public function __construct() {
		$this->db = new MM_WPFS_Database();
	}

	/**
	 * @return array
	 */
	public static function getLevels() {
		return array(
			self::LEVEL_DEBUG,
			self::LEVEL_INFO,
			self::LEVEL_WARN,
			self::LEVEL_ERROR
		);
	}

	public static function getLevelPriorities() {
		return array(
			self::LEVEL_DEBUG => self::LEVEL_PRIORITY_DEBUG,
			self::LEVEL_INFO  => self::LEVEL_PRIORITY_INFO,
			self::LEVEL_WARN  => self::LEVEL_PRIORITY_WARN,
			self::LEVEL_ERROR => self::LEVEL_PRIORITY_ERROR
		);
	}

	public static function getPriority( $level ) {
		if ( MM_WPFS_LoggerService::LEVEL_ERROR === $level ) {
			return MM_WPFS_LoggerService::LEVEL_PRIORITY_ERROR;
		} elseif ( MM_WPFS_LoggerService::LEVEL_WARN === $level ) {
			return MM_WPFS_LoggerService::LEVEL_PRIORITY_WARN;
		} elseif ( MM_WPFS_LoggerService::LEVEL_INFO === $level ) {
			return MM_WPFS_LoggerService::LEVEL_PRIORITY_INFO;
		} elseif ( MM_WPFS_LoggerService::LEVEL_DEBUG === $level ) {
			return MM_WPFS_LoggerService::LEVEL_PRIORITY_DEBUG;
		} else {
			return false;
		}
	}

	/**
	 * @return array
	 */
	public static function getModules() {
		return array(
			self::MODULE_PATCHER,
			self::MODULE_DATABASE,
			self::MODULE_ADMIN,
			self::MODULE_MY_ACCOUNT,
			self::MODULE_WEBHOOK_EVENT_HANDLER,
			self::MODULE_CHECKOUT_SUBMISSION
		);
	}

	public function createCheckoutSubmissionLogger( $class ) {
		return $this->createLogger( self::MODULE_CHECKOUT_SUBMISSION, $class );
	}

	/**
	 * @param string $module
	 * @param string $class always use __CLASS__ when possible
	 *
	 * @return MM_WPFS_Logger
	 */
	public function createLogger( $module, $class ) {
		return new MM_WPFS_Logger( $this, $module, $class );
	}

	/**
	 * @param string $class always use __CLASS__ when possible
	 *
	 * @return MM_WPFS_Logger
	 */
	public function createWebHookEventHandlerLogger( $class ) {
		return $this->createLogger( self::MODULE_WEBHOOK_EVENT_HANDLER, $class );
	}

	/**
	 * @param $class
	 *
	 * @return MM_WPFS_Logger
	 */
	public function createManageCardsAndSubscriptionsLogger( $class ) {
		return $this->createLogger( self::MODULE_MY_ACCOUNT, $class );
	}

	/**
	 * @param string $module one of the MODULE constant
	 * @param string $class always use __CLASS__ when possible
	 * @param string $function always use __FUNCTION__ when possible
	 * @param string $level
	 * @param string $message
	 * @param string $function
	 * @param null|Exception $exception
	 */
	public function log( $module, $class, $function, $level, $message, $exception = null ) {
        $this->db->insertLog( $module, $class, $function, $level, $message, is_null( $exception ) ? "" : $exception->getTraceAsString() );
	}

}

class MM_WPFS_Logger {

	/**
	 * @var MM_WPFS_LoggerService
	 */
	private $loggerService;
	private $module;
	private $class;
	private $level;

	/**
	 * MM_WPFS_Logger constructor.
	 *
	 * @param $loggerService
	 * @param $module
	 * @param $class
	 * @param null $level
	 */
	public function __construct( $loggerService, $module, $class, $level = null ) {
		$this->loggerService = $loggerService;
		$this->module        = $module;
		$this->class         = $class;
		$this->setLevel( $level );
	}

	public function setLevel( $level ) {
		if ( MM_WPFS_LoggerService::LEVEL_ERROR === $level ) {
			$this->level = MM_WPFS_LoggerService::LEVEL_ERROR;
		} elseif ( MM_WPFS_LoggerService::LEVEL_WARN === $level ) {
			$this->level = MM_WPFS_LoggerService::LEVEL_WARN;
		} elseif ( MM_WPFS_LoggerService::LEVEL_INFO === $level ) {
			$this->level = MM_WPFS_LoggerService::LEVEL_INFO;
		} elseif ( MM_WPFS_LoggerService::LEVEL_DEBUG === $level ) {
			$this->level = MM_WPFS_LoggerService::LEVEL_DEBUG;
		} else {
			$this->level = MM_WPFS_LoggerService::LEVEL_INFO;
		}
	}

	/**
	 * @return MM_WPFS_LoggerService
	 */
	public function getLoggerService() {
		return $this->loggerService;
	}

	/**
	 * @return mixed
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * @return mixed
	 */
	public function getClass() {
		return $this->class;
	}

	/**
	 * @param string $function always use __FUNCTION__ when possible
	 * @param $message
	 */
	public function info( $function, $message ) {
		if ( $this->isInfoEnabled() ) {
			$this->loggerService->log( $this->module, $this->class, $function, MM_WPFS_LoggerService::LEVEL_INFO, $message, null );
		}
	}

	public function isInfoEnabled() {
		return $this->isLevelEnabled( MM_WPFS_LoggerService::LEVEL_INFO );
	}

	protected function isLevelEnabled( $level ) {
		if ( MM_WPFS_LoggerService::getPriority( $level ) >= MM_WPFS_LoggerService::getPriority( $this->level ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @param string $function always use __FUNCTION__ when possible
	 * @param $message
	 */
	public function debug( $function, $message ) {
		if ( $this->isDebugEnabled() ) {
			$this->loggerService->log( $this->module, $this->class, $function, MM_WPFS_LoggerService::LEVEL_DEBUG, $message, null );
		}
	}

	public function isDebugEnabled() {
		return $this->isLevelEnabled( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	/**
	 * @param string $function always use __FUNCTION__ when possible
	 * @param $message
	 */
	public function warn( $function, $message ) {
		if ( $this->isWarnEnabled() ) {
			$this->loggerService->log( $this->module, $this->class, $function, MM_WPFS_LoggerService::LEVEL_WARN, $message, null );
		}
	}

	public function isWarnEnabled() {
		return $this->isLevelEnabled( MM_WPFS_LoggerService::LEVEL_WARN );
	}

	/**
	 * @param string $function always use __FUNCTION__ when possible
	 * @param $message
	 * @param null|Exception $exception
	 */
	public function error( $function, $message, Exception $exception = null ) {
		if ( $this->isErrorEnabled() ) {
			$this->loggerService->log( $this->module, $this->class, $function, MM_WPFS_LoggerService::LEVEL_ERROR, $message, $exception );
		}
	}

	public function isErrorEnabled() {
		return $this->isLevelEnabled( MM_WPFS_LoggerService::LEVEL_ERROR );
	}

}