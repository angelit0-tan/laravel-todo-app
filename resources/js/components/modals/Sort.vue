<template>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true" ref="createModalRef">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Sort task</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="description" class="form-label fw-bold">Sort By</label>
                                    <select class="form-select" aria-label="Default select example" v-model="order.sort_by">
                                        <option value="title">Title</option>
                                        <option value="description">Description</option>
                                        <option value="due_date">Due Date</option>
                                        <option value="created_at">Created Date</option>
                                        <option value="completed_at">Completed Date</option>
                                        <option value="task_priority">Priority</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="description" class="form-label fw-bold">Order By</label>
                                    <select class="form-select" aria-label="Default select example" v-model="order.sort_direction">
                                        <option value="ASC">Ascending Order</option>
                                        <option value="DESC">Descending Order</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" :disabled="isBusy" @click="sortTasks">Proceed</button>
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
            const createModalRef = ref(null);
            const myModal = ref(null);
            const dialog = inject('dialog');
            const currentPage = inject('currentPage');
    
            const order = ref({
                page: currentPage.value,
                data_type: props.dataType,
                sort_by: null,
                sort_direction: null,
            });
    
            // open modal
            const openModal = () => {
                myModal.value.show();
            }
    
            // Close modal
            const closeModal = () => {
                myModal.value.hide();
            }
    
            // Sort
            const sortTasks = async() => {
                isBusy.value = true;
                try {
                    await taskStore.getTasks(order.value);
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
                myModal.value = new Modal(createModalRef.value, {
                    keyboard: false
                });
            });
    
            return { createModalRef, openModal, closeModal, sortTasks, isBusy, order, taskDataTypes }
        },
    }
    </script>