<script setup lang="ts">
    // https://vuejs.org/api/sfc-script-setup.html#defineprops-defineemits
    // https://vuejs.org/guide/typescript/composition-api.html#typing-component-props
    const { href = '/', title = null } = defineProps<{
        href: string
        title?: string
    }>()
</script>

// https://vuejs.org/guide/essentials/class-and-style.html#with-components
<template>
    <a target="_self" class="app-link" :href="href" :title="title"><slot>{{ href }}</slot></a>
</template>
