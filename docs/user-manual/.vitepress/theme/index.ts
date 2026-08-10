// https://vitepress.dev/guide/extending-default-theme#customizing-css
import type {Theme} from 'vitepress'
import DefaultTheme from 'vitepress/theme'
import './custom.css'
import AppLink from "../components/AppLink.vue";
import AppMenuLink from "../components/AppMenuLink.vue";

export default {
  extends: DefaultTheme,
  enhanceApp({ app }) {
    // register your custom global components
    app.component('AppLink', AppLink)
    app.component('AppMenuLink', AppMenuLink)
  }
} satisfies Theme
