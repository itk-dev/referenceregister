<script setup lang="ts">
    import AppLink from "./AppLink.vue";

    const { href = '/', title = null } = defineProps<{
        href: string
        title?: string
    }>()
</script>

<template>
    <AppLink class="app-menu-link" :href="href" :title="title"><slot></slot></AppLink>
</template>
