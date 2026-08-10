import { defineConfig } from 'vitepress'
import { withMermaid } from 'vitepress-plugin-mermaid'

// https://vitepress.dev/reference/site-config
export default withMermaid(
    defineConfig({
        title: "Referenceregister",
        description: "Referenceregister",

        lang: 'da-DK',
        srcDir: '.',
        outDir: '../../public/user-manual',
        base: '/user-manual/',
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
                                link: '/en/manager',
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
                                link: '/da/bruger',
                            },
                            {
                                text: 'Leder',
                                link: '/da/leder',
                            },
                            {
                                text: 'Login',
                                link: '/da/login',
                            },
                        ],
                    },
                    // { text: 'Referenceregister', link: '/', target: '_self' },
                ],
            },

            docFooter: {
                prev: false,
                next: false,
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
