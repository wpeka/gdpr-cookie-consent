/**
 * Admin Block JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin
 * @author     wpeka <https://club.wpeka.com>
 */

(function( $ ) {
	const {registerBlockType} = wp.blocks; // Blocks API.
	const {createElement}     = wp.element; // React.createElement.
	const {__}                = wp.i18n; // Translation functions.
	const {InspectorControls,withColors,PanelColorSettings,getColorClassName} = wp.editor; // Block inspector wrapper.
	const {SelectControl,ServerSideRender}                                    = wp.components; // Block inspector wrapper.

	registerBlockType(
		'gdpr/block',
		{
			title: __( 'GDPR Cookie Details' ),
			category:  __( 'common' ),
			keywords: [
			__( 'gdpr' ),
			__( 'cookie' ),
			__( 'cookie links' )
			],
			edit( props ){
				return createElement(
					'div',
					{},
					[
					// Preview will go here.
					createElement(
						ServerSideRender,
						{
							block: 'gdpr/block',
							key:'gdpr'
						}
					)
					]
				)
			},
			save(){
				return null; // Save has to exist. This all we need.
			}
		}
	);
})( jQuery );
