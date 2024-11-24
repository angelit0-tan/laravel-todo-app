<template>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true" ref="createFilterModalRef">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Filter task</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="title" class="form-label fw-bold">Title</label>
                                    <input type="text" class="form-control" id="title" v-model="search.title"/>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="description" class="form-label fw-bold">Description</label>
                                    <input type="text" class="form-control" id="description" v-model="search.description"/>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="description" class="form-label fw-bold">Priority</label>
                                    <select class="form-select" aria-label="Default select example" v-model="search.priority">
                                        <option v-for="priority in taskPriority" :key="priority" :value="priority"> 
                                            {{ taskIdentity(priority).status }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3" v-if="[taskDataTypes.All, taskDataTypes.Completed].includes(parseInt(dataType))">
                                <label for="description" class="form-label fw-bold">Completed date</label>
                                <div class="col">
                                    <label for="completed_date_from" class="form-label">From</label>
                                    <input type="date" class="form-control" id="completed_date_from" v-model="search.completed_date_from"/>
                                </div>
                                <div class="col">
                                    <label for="completed_date_to" class="form-label">To</label>
                                    <input type="date" class="form-control" id="completed_date_to" v-model="search.completed_date_to"/>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="form-label fw-bold">Due date</label>
                                <div class="col">
                                    <label for="due_date_from" class="form-label">From</label>
                                    <input type="date" class="form-control" id="due_date_from" v-model="search.due_date_from"/>
                                </div>
                                <div class="col">
                                    <label for="due_date_to" class="form-label">To</label>
                                    <input type="date" class="form-control" id="due_date_to" v-model="search.due_date_to"/>
                                </div>
                            </div>
                            <div class="row mb-3" v-if="[taskDataTypes.All, taskDataTypes.Archived].includes(parseInt(dataType))">
                                <label class="form-label fw-bold">Archive date</label>
                                <div class="col">
                                    <label for="archive_date_from" class="form-label">From</label>
                                    <input type="date" class="form-control" id="archive_date_from" v-model="search.archive_date_from"/>
                                </div>
                                <div class="col">
                                    <label for="archive_date_to" class="form-label">To</label>
                                    <input type="date" class="form-control" id="archive_date_to" v-model="search.archive_date_to"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" :disabled="isBusy" @click="searchTasks">Search</button>
                    </div>
                </div>
            </div>
        </div>
    </template>
    <script>
    import { inject, onMounted, ref } from 'vue'
    import Modal from 'bootstrap/js/dist/modal';
    import { useTaskStore } from '@js/stores/TaskStore';
    import { taskDataTypes } from '@js/components/composables/TaskStatus.js';
    import taskStatus from '@js/components/composables/TaskPriority.js';
    
    export default {
        props:{
            dataType: {
                type: Number,
                default: 0
            }
        },
        setup(props) {
            const taskStore = useTaskStore();
            const isBusy = ref(false);
            const createFilterModalRef = ref(null);
            const myModal = ref(null);
            const dialog = inject('dialog');
            const { taskPriority, taskIdentity } = taskStatus();
    
            const search = ref({
                page: 1,
                data_type: props.dataType,
                title: null,
                description: null,
                priority: null,
                completed_date_from: null,
                completed_date_to: null,
                due_date_from: null,
                due_date_to: null,
                archive_date_from: null,
                archive_date_to: null,
            });
    
            // open modal
            const openModal = () => {
                reset();
                myModal.value.show();
            }
    
            // Close modal
            const closeModal = () => {
                myModal.value.hide();
            }
    
            // Reset fields
            const reset = () => {
                search.value.title = null;
                search.value.description = null;
                search.value.priority = null;
                search.value.completed_date_from = null;
                search.value.completed_date_to = null;
                search.value.due_date_from = null;
                search.value.due_date_to = null;
                search.value.archive_date_from = null;
                search.value.archive_date_to = null;
            }
    
            // Search
            const searchTasks = async() => {
                isBusy.value = true;
                try {
                    await taskStore.getTasks(search.value);
                    isBusy.value = false;
                } catch (e) {
                    isBusy.value = false;
                    dialog.show(`${e.response?.data?.message || 'Error encountered'}`, {
                        title: 'Error',
                        buttons: [
                            {
                                label: 'Close',
                                class: 'btn-light',
                                handler: (closeModal) => {
                                    closeModal();
                                }
                            },
                        ],
                    });
                }
                closeModal();
            }
    
            // Mounted
            onMounted(() => {
                myModal.value = new Modal(createFilterModalRef.value, {
                    keyboard: false
                });
            });
    
            return { createFilterModalRef, openModal, closeModal, searchTasks, isBusy, search, taskDataTypes, taskPriority, taskIdentity }
        },
    }
    </script>