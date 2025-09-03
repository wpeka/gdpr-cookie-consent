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
    var el = wp.element.createElement; // alias
    const { ServerSideRender } = wp.editor;// Block ServerSideRender wrapper.

    const icon = el(
		'svg',
		{
			width: 40,
			height: 41,
			viewBox: '0 0 40 41',
			fill: 'none',
			xmlns: 'http://www.w3.org/2000/svg',
		},
		[
			el('path', {
				key: 'bg',
				d: 'M0 3.24914C0 1.73083 1.23083 0.5 2.74914 0.5H37.2509C38.7692 0.5 40 1.73083 40 3.24914V37.7509C40 39.2692 38.7692 40.5 37.2509 40.5H2.74914C1.23083 40.5 0 39.2692 0 37.7509V3.24914Z',
				fill: '#074EA8',
			}),
			el('path', {
				key: 'text1',
				d: 'M7.77697 19.6468L3.63623 5.15527H6.9785L9.37391 15.2243H9.49403L12.1368 5.15527H14.9985L17.6342 15.2455H17.7614L20.1568 5.15527H23.4991L19.3583 19.6468H16.3764L13.6206 10.1721H13.5076L10.7589 19.6468H7.77697Z',
				fill: 'white',
			}),
			el('path', {
				key: 'text2',
				d: 'M24.9707 19.6197V5.12817H30.6801C31.7777 5.12817 32.7128 5.33809 33.4854 5.75793C34.2579 6.17305 34.8468 6.75092 35.2519 7.49153C35.6617 8.22743 35.8666 9.07654 35.8666 10.0389C35.8666 11.0012 35.6594 11.8503 35.2448 12.5862C34.8303 13.3221 34.2297 13.8952 33.443 14.3056C32.661 14.716 31.7141 14.9212 30.6024 14.9212H26.9633V12.4659H30.1078C30.6966 12.4659 31.1818 12.3645 31.5634 12.1616C31.9497 11.9541 32.237 11.6687 32.4254 11.3054C32.6186 10.9375 32.7152 10.5153 32.7152 10.0389C32.7152 9.5577 32.6186 9.13786 32.4254 8.77935C32.237 8.41612 31.9497 8.13544 31.5634 7.93731C31.1771 7.73447 30.6872 7.63305 30.0936 7.63305H28.0303V19.6197H24.9707Z',
				fill: 'white',
			}),
			el('ellipse', {
				key: 'dot1',
				cx: 10.8749,
				cy: 24.6618,
				rx: 3.25331,
				ry: 3.25331,
				fill: 'white',
			}),
			el('ellipse', {
				key: 'dot2',
				cx: 10.8749,
				cy: 33.0746,
				rx: 3.25331,
				ry: 3.25331,
				fill: 'white',
			}),
			el('path', {
				key: 'text3',
				d: 'M24.9961 36.2515V21.76H30.7055C31.8031 21.76 32.7382 21.9699 33.5107 22.3898C34.2833 22.8049 34.8721 23.3827 35.2772 24.1234C35.6871 24.8593 35.892 25.7084 35.892 26.6707C35.892 27.633 35.6847 28.4821 35.2702 29.218C34.8556 29.9539 34.255 30.5271 33.4683 30.9375C32.6863 31.3479 31.7395 31.5531 30.6278 31.5531H26.9887V29.0977H30.1331C30.722 29.0977 31.2072 28.9963 31.5887 28.7935C31.975 28.5859 32.2624 28.3005 32.4508 27.9373C32.6439 27.5693 32.7405 27.1471 32.7405 26.6707C32.7405 26.1895 32.6439 25.7697 32.4508 25.4112C32.2624 25.0479 31.975 24.7673 31.5887 24.5691C31.2025 24.3663 30.7125 24.2649 30.119 24.2649H28.0557V36.2515H24.9961Z',
				fill: 'white',
			}),
			el('path', {
				key: 'text4',
				d: 'M16.4592 36.2486L16.459 21.4084H19.5186L19.5188 33.3759L23.4535 33.3758V36.2485L16.4592 36.2486Z',
				fill: 'white',
			}),
		]
	);

    registerBlockType('gdpr/block', {
        title: __('WP Cookie Policy Details'),
        description: __('A custom block to generate 3rd Party cookie table.'),
        icon,
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
