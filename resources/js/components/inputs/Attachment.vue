<template>
    <div>
        <template v-if="!isEditing">
            <div class="row">
                <div class="col-md-6">
                    <a :href="`/storage/${attachment.name}`"> {{ attachment.name }} </a>
                </div>
                <div class="col-md-6 d-flex">
                    <button class="btn btn-sm btn-primary me-1" @click="edit">
                        Edit
                    </button>
                    <button class="btn btn-sm btn-primary me-1" @click="remove">
                        Delete 
                    </button>
                    <button class="btn btn-sm btn-primary" @click="download">
                        Download 
                    </button>
                </div>
            </div>
        </template>
        <template v-else>
            <div class="d-flex align-items-center">
                <input id="file-input" class="form-control" type="file" :accept="fileTypes" @change="attachFile"/>
                <button class="btn btn-sm" @click="isEditing=false">
                    Cancel
                </button>
            </div>
            <div v-if="error" class="mb-2">
                <span class="text-danger">{{ error }}</span>
            </div>
        </template>
    </div>
</template>
<script>
import { ref } from 'vue';
import http from '@js/http.js';
import { fileTypes, isValidFile } from '@js/components/composables/FileType.js';
export default {
    props: {
        attachment: {
            type: Object,
            default: () => {}
        }
    },
    emits: ['updateAttachment', 'removeAttachment'],
    setup(props, {emit}) {
        const isEditing = ref(false);
        const error = ref(null);

        // Edit file
        const edit = () => {
            isEditing.value = true;
        }

        // Attach file
        const attachFile = async(event) => {
            const hasInvalidFile = ref(false);
            // Check if uploaded file are valid
            for (const file of event.target.files) {
                if (!isValidFile(file)) {
                    hasInvalidFile.value = true;
                    break;
                }
            }

            // If has invalid file show error message
            if (hasInvalidFile.value) {
                error.value = "Error: Invalid file type";
            } else {
                // Upload files
                try {
                    for (const file of event.target.files) {
                        let data = new FormData();
                        data.append('file', file);
                        await http.post(`/api/upload`, data).then(function ({data}) {
                            emit('updateAttachment', props.attachment.id, data)
                        });
                    }
                } catch (e) {
                    error.value = `${e.response?.data?.message?.file[0] || 'Error encountered'}`;
                }
            }

        }
        
        // Delete attachment
        const remove = () => {
            emit('removeAttachment', props.attachment.id)
        }

        // Download
        const download = async() => {
            const blob = await (await fetch(`/storage/${props.attachment.name}`)).blob();
            const url = URL.createObjectURL(blob);
            Object.assign(document.createElement('a'), { href: url, download: props.attachment.name }).click();
            URL.revokeObjectURL(url);
        }

        return { isEditing, edit, download, attachFile, remove, fileTypes, isValidFile, error}
    },
}
</script>