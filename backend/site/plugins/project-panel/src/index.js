import Button from "./components/Button.vue";

window.panel.plugin("project/panel", {
	blocks: {
		button: Button,
	},
	components: {
		'k-block-type-heading': {
			extends: 'k-block-type-heading',
			methods: {
				updateDataAttributes() {
					const { alignment, style } = this.content;
					if (!this.$el) return;
					this.$el.dataset.alignment = alignment
					this.$el.dataset.style = style
				}
			},
			watch: {
				"content.alignment"() {
					this.updateDataAttributes();
				},
				"content.style"() {
					this.updateDataAttributes();
				}
			},
			mounted() {
				this.updateDataAttributes()
			}
		},
		'k-block-type-text': {
			extends: 'k-block-type-text',
			methods: {
				updateDataAttributes() {
					const { alignment, style } = this.content;
					if (!this.$el) return;
					this.$el.dataset.alignment = alignment
					this.$el.dataset.style = style
				}
			},
			watch: {
				"content.alignment"() {
					this.updateDataAttributes();
				},
				"content.style"() {
					this.updateDataAttributes();
				}
			},
			mounted() {
				this.updateDataAttributes()
			}
		},
		'k-block-type-list': {
			extends: 'k-block-type-list',
			methods: {
				updateDataAttributes() {
					const { style } = this.content;
					if (!this.$el) return;
					this.$el.dataset.style = style
				}
			},
			watch: {
				"content.style"() {
					this.updateDataAttributes();
				}
			},
			mounted() {
				this.updateDataAttributes()
			}
		},
		'k-block-type-image': {
			extends: 'k-block-type-image',
			methods: {
				updateDataAttributes() {
					if (!this.$el) return;
					const { maxwidth, alignment } = this.content;
					// reflect the block's max-width and alignment in the panel preview
					if (maxwidth) {
						this.$el.style.setProperty('--block-maxwidth', `${maxwidth}px`)
					} else {
						this.$el.style.removeProperty('--block-maxwidth')
					}
					this.$el.dataset.alignment = alignment
				}
			},
			watch: {
				"content.maxwidth"() {
					this.updateDataAttributes();
				},
				"content.alignment"() {
					this.updateDataAttributes();
				}
			},
			mounted() {
				this.updateDataAttributes()
			}
		},
	}
});
