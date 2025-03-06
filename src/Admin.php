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
			'soft-hyphenate',
			[ __CLASS__, 'display_settings_page' ]
		);
	}

	/**
	 * Initialize the settings.
	 */
	public static function settings_init(): void {
		register_setting( 'soft-hyphenate', 'hp-soft-hyphenate' );

		add_settings_section(
			'hyphenation-suggestion-section',
			__( 'Hyphenation Suggestions', 'soft-hyphenate' ),
			[ __CLASS__, 'section_callback' ],
			'soft-hyphenate'
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
				settings_fields( 'soft-hyphenate' );

				do_settings_sections( 'soft-hyphenate' );

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
		printf( '<p>%s</p>', esc_html__( 'Enter your hyphenation suggestions (e.g. "hyph-en-ate") below, one word per line.', 'soft-hyphenate' ) );
	}

	/**
	 * Render the field.
	 */
	public static function display_hyphenation_suggestion_field(): void {
		$hyphenation_suggestion = get_option( 'hp-soft-hyphenate', '' );

		if ( ! is_scalar( $hyphenation_suggestion ) ) {
			$hyphenation_suggestion = '';
		}

		printf(
			'<textarea name="hp-soft-hyphenate" id="hp-soft-hyphenate" rows="20" cols="50">%s</textarea>',
			esc_textarea( (string) $hyphenation_suggestion )
		);
	}
}
