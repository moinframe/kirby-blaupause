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
          if(!this.$el) return;
          this.$el.dataset.alignment = alignment
          this.$el.dataset.style = style
        }
      },
      watch: {
        content: {
          handler() {
            this.updateDataAttributes();
          }
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
          if(!this.$el) return;
          this.$el.dataset.alignment = alignment
          this.$el.dataset.style = style
        }
      },
      watch: {
        content: {
          handler() {
            this.updateDataAttributes();
          }
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
          if(!this.$el) return;
          this.$el.dataset.style = style
        }
      },
      watch: {
        content: {
          handler() {
            this.updateDataAttributes();
          }
        }
      },
      mounted() {
        this.updateDataAttributes()
      }
    },
  }
});
