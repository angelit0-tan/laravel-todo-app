<template>
    <div>
        <template v-if="type === 'text-area'">
            <textarea class="form-control" :rows="rows" v-model="localValue"/>
        </template>
        <template v-else>
            <input :type="type" class="w-full border rounded p-2 outline-none focus:shadow-outline" v-model="localValue" :placeholder="placeholder"/>
        </template>
        <small class="text-red" v-if="hasError"> {{ errors[name][0] }}</small>
    </div>
</template>
<script>
import { computed, ref, watch } from 'vue'

export default {
    props: {
        type: {
            type: String,
            default: 'text'
        },
        name: {
            type: String,
            default: null
        },
        errors: {
            type: Array,
            default: []
        },
        modelValue: {
            type: String,
            default: null,
        },
        rows:{
            type: Number,
            default: 1
        },
        placeholder: {
            type: String,
            defualt: null
        }
    },
    emits: ['update:modelValue'],
    setup(props, {emit}) {

        const localValue = ref(null);

        // Check if error match the field
        const hasError = computed(() => {
            return props.errors && props.errors[props.name] ? props.errors[props.name][0] != null : false;
        });

        // Watchers
        watch(localValue, (newLocalValue) => {
            emit('update:modelValue', newLocalValue);
        });

        watch (() => props.modelValue, (newValue) => {
            localValue.value = newValue;
        });

        return { localValue, hasError }
    },
}
</script>