<template>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true" ref="createTaskModalRef">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Task</h5>
                    <button type="button" class="btn-close" @click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="title" >Title </label>
                        <input-text id="title" name="title" v-model="task.title" :errors="errors"/>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input-text id="description" type="text-area" name="description" v-model="task.description" rows="3" :errors="errors"/>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input-text id="due_date" type="date" name="due_date" v-model="task.due_date" :errors="errors"/>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Task Priority</label>
                        <select class="form-select" aria-label="Default select example" v-model="task.task_priority">
                            <option v-for="priority in taskPriority" :key="priority" :value="priority"> 
                                {{ taskIdentity(priority).status }}
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags</label>
                        <input type="text" class="form-control" id="tags" placeholder="coding" v-model="tag" @keydown.enter.prevent="addTags"/>
                        <div 
                        >
                            <span class="d-inline-block badge rounded-pill bg-secondary me-1 mt-2" v-for="tag in task.tags"
                            :key="tag">
                                <button type="button" class="btn ms-1 p-0 text-white tag" @click="removeTag(tag.id)"> {{ tag.name }} <i class="bi bi-x-circle-fill"/></button>
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="attachments" class="form-label">Attachments</label>
                        <input id="file-input" class="form-control" type="file" :accept="fileTypes" multiple @change="attachFiles"/>
                        <div v-if="errors && errors.length" class="mb-2">
                            <span class="text-danger">{{ errors }}</span>
                        </div>
                        <div class="my-3">
                            <template v-for="attachment in task.attachments" :key="attachment.id">
                                <attachment class="mb-2" :attachment="attachment" @updateAttachment="updateAttachment" @removeAttachment="removeAttachment"/>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" :disabled="isBusy" @click="saveTask">Save</button>
                    <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { inject, onMounted, ref } from 'vue';
import Attachment from '@js/Components/Inputs/Attachment.vue';
import http from '@js/http.js';
import Modal from 'bootstrap/js/dist/modal';
import { useTaskStore } from '@js/Stores/TaskStore';
import taskStatus from '@js/Components/Composables/TaskPriority.js';
import { fileTypes, isValidFile } from '@js/Components/Composables/FileType.js';

export default {
    props:{
        dataType: {
            type: Number,
            default: 0
        }
    },
    components: { Attachment },
    setup(props) {
        const taskStore = useTaskStore();
        const errors = ref(null);
        const createTaskModalRef = ref(null);
        const myModal = ref(null);
        const tag = ref('');
        const isBusy = ref(false);
        const dialog = inject('dialog');
        const { taskPriority, taskIdentity } = taskStatus();
        const task = ref({
            id: null,
            title: null,
            description: null,
            due_date: null,
            task_priority: null,
            tags: [],
            attachments: [],
        });
        const currentPage = inject('currentPage');

        // Add tag
        const addTags = async() => {
            // Check with value and no duplicates
            if (tag.value.trim() && !task.value.tags?.map((i) => i.name).includes(tag.value)) {
                const data = await taskStore.addTag({ 'name': tag.value });
                task.value.tags.push(data);
            }
            tag.value = null;
        }

        // Remove tag
        const removeTag = (id) => {
            task.value.tags = task.value.tags.filter((i) => i.id !== id);
        }

        // form reset
        const reset = () => {
            task.value.id = null;
            task.value.title = null;
            task.value.description = null;
            task.value.due_date = null;
            task.value.tags = [];
            task.value.attachments = [];
            task.value.task_priority = null;
            errors.value = [];
            isBusy.value = false;
        }

        // open modal
        const openModal = () => {
            reset();
            myModal.value.show();
        }

        // Close modal
        const closeModal = () => {
            reset();
            myModal.value.hide();
        }

        // Add task
        const saveTask = async() => {
            errors.value = [];
            isBusy.value = true;

            try {
                const data = {
                    'id' : task.value?.id,
                    'title' : task.value.title,
                    'description' : task.value.description,
                    'due_date' : task.value.due_date,
                    'task_priority' : task.value.task_priority,
                    'tags' : task.value.tags?.map((i) => i.id),
                    'attachments' : task.value.attachments?.map((i) => i.id)
                }

                // If theres id present, we only need to update the data
                if (task.value.id){
                    await taskStore.updateTask(data);
                } else { // otherwise, add new data
                    await taskStore.addTask(data);
                }

                await taskStore.getTasks({page: currentPage.value, data_type: props.dataType});

                dialog.show(`${task.value.id ? 'Task successfully updated' : 'You have successfully added a task'} `, {
                    title: 'Success',
                    buttons: [
                    {
                        label: 'Close',
                        class: 'btn-primary',
                        handler: (closeModal) => {
                            closeModal();
                        }
                    },
                    ],
                });
                myModal.value.hide();
                reset();
            } catch (e) {
                errors.value = e.response?.data?.message;
                isBusy.value = false;
            }
        }

        // Display task details
        const showDetails = async(id) => {
            const data = await taskStore.getTask(id);
            task.value = data;
            myModal.value.show();
        }

        // Upload of task files
        const attachFiles = async(event) => {
            errors.value = null;
            const hasInvalidFile = ref(false);
            
            // Check if uploaded file are valid
            for (const file of event.target.files) {
                if (!isValidFile(file)) {
                    console.log(file.type)
                    hasInvalidFile.value = true;
                    break;
                }
            }

            // If has invalid file show error message
            if (hasInvalidFile.value) {
                errors.value = `File type is not supported in the lists ${fileTypes}`;
            } else {
                // Upload files
                try {
                    for (const file of event.target.files) {
                        let data = new FormData();
                        data.append('file', file);
                        await http.post('/api/upload',data).then(function ({data}) {
                            task.value.attachments.push(data);
                        });
                    }
                } catch (e) {
                    errors.value = `${e.response?.data?.message?.file[0] || 'Error encountered'}`
                }
            }
        }

        // When attachment is updated, it needs to be updated in the model
        const updateAttachment = (attachmentId, newData) => {
            const index = task.value.attachments.findIndex((i) =>i.id === attachmentId);
            task.value.attachments[index] = newData;
        }

        // Remove attachment
        const removeAttachment = (attachmentId) => {
            task.value.attachments = task.value.attachments.filter((i) => i.id !== attachmentId);
        }
        
        // Mounted
        onMounted(() => {
            myModal.value = new Modal(createTaskModalRef.value, {
                keyboard: false
            });
        });

        return { 
                createTaskModalRef, task, tag, fileTypes, isValidFile,
                openModal, closeModal, showDetails,
                addTags, removeTag, saveTask, errors, isBusy,
                taskPriority, taskIdentity, attachFiles, updateAttachment, removeAttachment
        }
    },
}
</script>
<style scoped>
    .tag {
        font-size: inherit;
    }
</style>