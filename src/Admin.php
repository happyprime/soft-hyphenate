<?php
/**
 * Manage admin settings for the plugin.
 *
 * @package soft-hyphenate
 */

namespace SoftHyphenate;

/**
 * Manage admin settings for the plugin.
 */
class Admin {

	const OPTION_PREFIX = 'hp-soft-';
	const MENU_SLUG = 'soft-hyphenate';
	const PAGE_SLUG = 'soft-hyphenate';
	const SECTION_ID = 'hyphenation-suggestion-section';
	const OPTION_GROUP = 'soft-hyphenate';

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
			__( 'Soft Hyphenate', 'soft-hyphenate' ),
			__( 'Soft Hyphenate', 'soft-hyphenate' ),
			'manage_options',
			self::MENU_SLUG,
			[ __CLASS__, 'display_settings_page' ]
		);
	}

	/**
	 * Initialize the settings.
	 */
	public static function settings_init(): void {
		register_setting(
			self::OPTION_GROUP,
			slef::OPTION_PREFIX . 'hyphenate',
			[
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_textarea_field',
			]
		);

		add_settings_section(
			self::SECTION_ID,
			__( 'Hyphenation Suggestions', 'soft-hyphenate' ),
			[ __CLASS__, 'section_callback' ],
			self::PAGE_SLUG
		);

		add_settings_field(
			'hyphenation-suggestions',
			'',
			[ __CLASS__, 'display_hyphenation_suggestion_field' ],
			'soft-hyphenate',
			'hyphenation-suggestion-section'
		);
	}

	/**
	 * Render the settings page.
	 */
	public static function display_settings_page(): void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Soft Hyphenate Settings', 'soft-hyphenate' ); ?></h1>
			<form action="options.php" method="post">
			<?php
				settings_fields( self::OPTION_GROUP );

				do_settings_sections( self::PAGE_SLUG );

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
			esc_html__( 'Enter your hyphenation suggestions (e.g. "hyph-en-ate") below, one word per line.', 'soft-hyphenate' )
		);
	}

	/**
	 * Render the field.
	 */
	public static function display_hyphenation_suggestion_field(): void {
		$hyphenation_suggestion = get_option( self::OPTION_PREFIX . 'hyphenate', '' );

		if ( ! is_scalar( $hyphenation_suggestion ) ) {
			$hyphenation_suggestion = '';
		}

		printf(
			'<textarea name="hp-soft-hyphenate" id="hp-soft-hyphenate" rows="20" cols="50">%s</textarea>',
			esc_textarea( (string) $hyphenation_suggestion )
		);
	}
}
