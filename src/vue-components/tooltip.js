export default {
	name: 'tooltip',
	template: `<span class="gdpr-form-tooltip">
					<img class="gdpr-tooltip-image" :src="tooltip.default" alt="WPCS Tooltip icon">
					<span class="gdpr-form-tooltiptext">{{text}}</span>
				</span>`,
	props: {
		text: {
			type: String,
			default: '',
		}
	},
	data() {
		return {
			tooltip: require('../../admin/images/tooltip-icon.svg')
		}
	}
}