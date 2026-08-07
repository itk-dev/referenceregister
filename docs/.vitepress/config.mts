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
            // nav: [
            //     { text: 'Referenceregister', link: '/', target: '_self' },
            //     // { text: 'Sign Out', link: '/oauth2/sign_out', target: '_self' },
            // ],

            sidebar: {
                '/en/': [
                    {
                        text: 'User manual',
                        items: [
                            // {
                            //     text: 'User',
                            //     link: '/en/user-manual/user',
                            // },
                            {
                                text: 'Manager',
                                link: '/en/user-manual/manager',
                            },
                        ],
                    },
                ],
                '/da/': [
                    {
                        text: 'Brugervejledning',
                        items: [
                            {
                                text: 'Bruger',
                                link: '/da/brugervejledning/bruger',
                            },
                            {
                                text: 'Leder',
                                link: '/da/brugervejledning/leder',
                            },
                        ],
                    },
                    // { text: 'Referenceregister', link: '/', target: '_self' },
                ],
            },
        },

        // https://vitepress.dev/guide/i18n
        locales: {
            root: {
                label: 'Dansk',
                lang: 'da',

                themeConfig: {
                    outline: {
                        label: 'Indhold',
                    },
                },
            },
            en: {
                label: 'English',
                lang: 'en',
            },
        },
    }),
)
