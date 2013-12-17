<?php
defined('LOCALE') ? null : define('LOCALE', 'locale');

class I18n {
	
	private static $locale = 'en_US';
	
	private static $i18n = array();
	
	private static $i18n_files = array();

	/**
	 * Gets the current locale.
	 * GET -> POST -> SESSION -> COOKIE.
	 */
	public static function get_locale() {
		if (isset($_GET[LOCALE])) {
			return $_GET[LOCALE];
		} elseif (isset($_POST[LOCALE])) {
			return $_POST[LOCALE];
		} elseif (isset($_SESSION[LOCALE])) {
			return $_SESSION[LOCALE];
		} elseif (isset($_COOKIE[LOCALE])) {
			return $_COOKIE[LOCALE];
		}
		return self::$locale;
	}
	
	/**
	 * Retrieves the translation of $text. 
	 * If there is no translation, the original text is returned.
	 */
	public static function translate( $text, $locale = '' ) {
		if (empty($locale)) {
			$locale = self::get_locale();
		}
		if (isset(self::$i18n[$locale])) {
			if (isset(self::$i18n[$locale][$text])) {
				return self::$i18n[$locale][$text];
			} else {
				return $text;
			}
		} else {
			// only require once for those i18n configure files.
			$i18n_file = FIREFLY_BASE_DIR . DS . 'config' . DS . 'locales' . DS . $locale . '.php';
			if (!in_array($i18n_file, self::$i18n_files)) {
				array_push(self::$i18n_files, $i18n_file);
				if (file_exists($i18n_file)) {
					$i18n = array();
					require $i18n_file;
					self::$i18n[$locale] = $i18n;
					return self::translate($text, $locale);
				} else {
					Logger::warn("Could not load i18n config file: $i18n_file");
					echo 'File not exists: "' . $i18n_file . '"' ;
					return $text;
				}
			}
		}
	}
	
}


// facility functions
function _t($text, $locale = '') {
	return I18n::translate($text, $locale);
}

?>