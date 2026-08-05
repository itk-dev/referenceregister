import { defineConfig } from 'vitepress'
import { withMermaid } from 'vitepress-plugin-mermaid'

// https://vitepress.dev/reference/site-config
export default withMermaid(
defineConfig({
  title: "Referenceregister",
  description: "Referenceregister",

  lang: 'da-DK',
  srcDir: '.',
  outDir: '../public/docs',
  base: '/docs/',
  cleanUrls: true,

  themeConfig: {
  // https://stackoverflow.com/a/79386388
  logoLink: {
      link: '/',
      target: '_self' // explicit target is needed to prevent
                      // vitepress router from intercepting link
    },

    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Referenceregister', link: '/', target: '_self' },
      // { text: 'Sign Out', link: '/oauth2/sign_out', target: '_self' },
      { text: 'Brugervejledning', link: '/da/brugervejledning' }
    ],

//     sidebar: [
//       {
//         text: 'Examples',
//         items: [
//           { text: 'Markdown Examples', link: '/markdown-examples' },
//           { text: 'Runtime API Examples', link: '/api-examples' }
//         ]
//       }
//     ],
  }
}),
)
