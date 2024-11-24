<template>
    <slot/>
    <Dialog ref="dialogRef"/>
</template>
<script>
import { provide, ref } from 'vue'

export default {
    setup() {
        const dialogRef = ref(null);

        // Will be use to show dialog message
        const showDialog = (content, title, buttons) => {
            dialogRef.value.prompt(content, title, buttons);
        }

        provide('dialog', { show: showDialog })

        return { dialogRef }
    },
}
</script>