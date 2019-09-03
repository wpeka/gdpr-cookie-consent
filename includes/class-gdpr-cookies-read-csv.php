<?php
/**
 * The file that defines csv read functionality
 *
 * A class definition that includes attributes and functions used for csv read functionality.
 *
 * @link       https://club.wpeka.com
 * @since      1.9
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/includes
 * @author     wpeka <https://club.wpeka.com>
 */

/**
 * The csv read plugin class.
 *
 * This is used to define hooks for rest api.
 *
 * @since      1.9
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/includes
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookies_Read_Csv {
	const FIELD_START    = 0;
	const UNQUOTED_FIELD = 1;
	const QUOTED_FIELD   = 2;
	const FOUND_QUOTE    = 3;
	const FOUND_CR_Q     = 4;
	const FOUND_CR       = 5;

	/**
	 * File to be processed.
	 *
	 * @since 1.9
	 * @var string $file Filename.
	 */
	private $file;
	/**
	 * Separator used for data.
	 *
	 * @since 1.9
	 * @var string $sep Separator.
	 */
	private $sep;
	/**
	 * End of File.
	 *
	 * @since 1.9
	 * @var bool $eof End of File.
	 */
	private $eof;
	/**
	 * Number of characters.
	 *
	 * @since 1.9
	 * @var bool|string $nc Number of characters.
	 */
	private $nc;

	/**
	 * Gdpr_Cookies_Read_Csv constructor.
	 *
	 * @param Object $file_handle Open file to read from.
	 * @param string $sep Column separator character.
	 * @param string $skip Initial character sequence to skip if found, eg UTF-8 byte-order mark.
	 */
	public function __construct( $file_handle, $sep, $skip = '' ) {
		$this->file = $file_handle;
		$this->sep  = $sep;
		$this->nc   = fgetc( $this->file );
		// skip junk at start.
		$skip_length = strlen( $skip );
		for ( $i = 0; $i < $skip_length; $i++ ) {
			if ( $this->nc !== $skip[ $i ] ) {
				break;
			}
			$this->nc = fgetc( $this->file );
		}
		$this->eof = ( false === $this->nc );
	}

	/**
	 * Returns next character.
	 *
	 * @since 1.9
	 * @return bool|string
	 */
	private function next_char() {
		$c         = $this->nc;
		$this->nc  = fgetc( $this->file );
		$this->eof = ( false === $this->nc );
		return $c;
	}

	/**
	 * Get next record from CSV file.
	 *
	 * @since 1.9
	 * @return array|null
	 */
	public function get_row() {
		if ( $this->eof ) {
			return null;
		}

		$row   = array();
		$field = '';
		$state = self::FIELD_START;

		while ( 1 ) {
			$char = $this->next_char();

			if ( self::QUOTED_FIELD === $state ) {
				if ( false === $char ) {
					// EOF. (TODO: error case - no closing quote).
					$row[] = $field;
					return $row;
				}
				// Fall through to accumulate quoted chars in switch() {...}.
			} elseif ( false === $char || "\n" === $char ) {
				// End of record.
				// (TODO: error case if $state==self::FIELD_START here - trailing comma).
				$row[] = $field;
				return $row;
			} elseif ( "\r" === $char ) {
				// Possible start of \r\n line end, but might be just part of foo\rbar.
				$state = ( self::FOUND_QUOTE === $state ) ? self::FOUND_CR_Q : self::FOUND_CR;
				continue;
			} elseif ( $char === $this->sep && ( self::FIELD_START === $state || self::FOUND_QUOTE === $state || self::UNQUOTED_FIELD === $state ) ) {
				// End of current field, start of next field.
				$row[] = $field;
				$field = '';
				$state = self::FIELD_START;
				continue;
			}

			switch ( $state ) {

				case self::FIELD_START:
					if ( '"' === $char ) {
						$state = self::QUOTED_FIELD;
					} else {
						$state  = self::UNQUOTED_FIELD;
						$field .= $char;
					}
					break;

				case self::QUOTED_FIELD:
					if ( '"' === $char ) {
						$state = self::FOUND_QUOTE;
					} else {
						$field .= $char;
					}
					break;

				case self::UNQUOTED_FIELD:
					$field .= $char;
					// (TODO: error case if '"' in middle of unquoted field).
					break;

				case self::FOUND_QUOTE:
					// Found '"' escape sequence.
					$field .= $char;
					$state  = self::QUOTED_FIELD;
					// (TODO: error case if $char!='"' - non-separator char after single quote).
					break;

				case self::FOUND_CR:
					// Lone \rX instead of \r\n. Treat as literal \rX. (TODO: error case?).
					$field .= "\r" . $char;
					$state  = self::UNQUOTED_FIELD;
					break;

				case self::FOUND_CR_Q:
					// (TODO: error case: "foo"\rX instead of "foo"\r\n or "foo"\n). // phpcs.ignore
					$field .= "\r" . $char;
					$state  = self::QUOTED_FIELD;
					break;
			}
		}
	}
}

