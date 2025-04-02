<?php
/**
 * Manage admin settings for the plugin.
 *
 * @package HappyPrime\SoftHyphenate
 */

namespace HappyPrime\SoftHyphenate;

/**
 * Manage admin settings for the plugin.
 */
class Admin {

	/**
	 * The unique identifier for the settings page.
	 */
	const SETTINGS_PAGE = PREFIX . SLUG . '-page';

	/**
	 * The unique identifier for the option group.
	 */
	const OPTION_GROUP = PREFIX . SLUG . '-option-group';

	/**
	 * Initialize customizations in the WordPress admin.
	 */
	public static function init(): void {
		add_action( 'admin_menu', [ __CLASS__, 'add_settings_page' ] );
		add_action( 'admin_init', [ __CLASS__, 'settings_init' ] );
	}

	/**
	 * Add the settings page.
	 */
	public static function add_settings_page(): void {
		add_options_page(
			__( 'Soft Hyphenate', 'hp-soft-hyphenate' ),
			__( 'Soft Hyphenate', 'hp-soft-hyphenate' ),
			'manage_options',
			self::SETTINGS_PAGE,
			[ __CLASS__, 'display_settings_page' ]
		);
	}

	/**
	 * Initialize the settings.
	 */
	public static function settings_init(): void {
		register_setting(
			self::OPTION_GROUP,
			OPTION_NAME,
			[
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_textarea_field',
			]
		);

		add_settings_section(
			'hyphenation-suggestions',
			__( 'Hyphenation Suggestions', 'hp-soft-hyphenate' ),
			[ __CLASS__, 'section_callback' ],
			self::SETTINGS_PAGE
		);

		add_settings_field(
			'hyphenation-suggestions-input',
			'',
			[ __CLASS__, 'display_hyphenation_suggestion_field' ],
			self::SETTINGS_PAGE,
			'hyphenation-suggestions'
		);
	}

	/**
	 * Render the settings page.
	 */
	public static function display_settings_page(): void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Soft Hyphenate Settings', 'hp-soft-hyphenate' ); ?></h1>
			<form action="options.php" method="post">
			<?php
				settings_fields( self::OPTION_GROUP );
				do_settings_sections( self::SETTINGS_PAGE );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the section.
	 */
	public static function section_callback(): void {
		printf(
			'<p>%s</p>',
			esc_html__( 'Enter your hyphenation suggestions (e.g. "hyph-en-ate") below, one word per line.', 'hp-soft-hyphenate' )
		);
	}

	/**
	 * Render the field.
	 */
	public static function display_hyphenation_suggestion_field(): void {
		$hyphenation_suggestion = get_option( OPTION_NAME, '' );

		if ( ! is_scalar( $hyphenation_suggestion ) ) {
			$hyphenation_suggestion = '';
		}

		printf(
			'<textarea name="%s" id="%s" rows="20" cols="50">%s</textarea>',
			esc_attr( OPTION_NAME ),
			esc_attr( OPTION_NAME ),
			esc_textarea( (string) $hyphenation_suggestion )
		);
	}
}
