/**
 * Admin Block JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin
 * @author     wpeka <https://club.wpeka.com>
 */

 (function($) {
    const { registerBlockType } = wp.blocks;// Blocks API.
    const { createElement } = wp.element;// React.createElement.
    const { __ } = wp.i18n;// Translation functions.
    const { ServerSideRender } = wp.editor;// Block ServerSideRender wrapper.

    registerBlockType('gdpr/block', {
        title: __('WP Cookie Details'),
        category: __('common'),
        keywords: [__('gdpr'), __('cookie'), __('cookie links')],
        edit(props) {
            return createElement(
                'div',
                {},
                createElement(ServerSideRender, {// Preview will go here.
                    block: 'gdpr/block',
                    key: 'gdpr'
                })
            );
        },
        save() {
            return null; // Save has to exist. This all we need.
        }
    });
})(jQuery);
