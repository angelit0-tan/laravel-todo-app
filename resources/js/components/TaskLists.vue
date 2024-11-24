<template>
    <div class="container">
        <div class="mb-3">
             <h1>
                <template v-if="Number(dataType) == taskDataTypes.All">
                    My Tasks 
                </template>
                 <template v-if="Number(dataType) == taskDataTypes.Completed">
                    Completed Tasks 
                </template>
                <template v-if="Number(dataType) == taskDataTypes.Todo">
                    Todo Tasks 
                </template>
                <template v-if="Number(dataType) == taskDataTypes.Archived">
                    Archived Tasks 
                </template>
            </h1>
        </div>
        <div class="mb-3">
            <p v-if="Number(dataType) == taskDataTypes.All"> This contains all the tasks either complete / incomplete / archived tasks.</p>
            <p v-if="Number(dataType) == taskDataTypes.Archived"> This contains tasks for archived only.</p>
            <p v-if="Number(dataType) == taskDataTypes.Completed"> This contains tasks for completed only.</p>
            <p v-if="Number(dataType) == taskDataTypes.Todo"> This contains tasks for incompleted / todo only.</p>
        </div>
        <div class="mb-3">
            <div class="d-md-flex">
                <div class="flex-md-grow-1">
                    <button type="button" class="btn btn-primary" @click="createTask" :disabled="Number(dataType) !== taskDataTypes.All"> Create Task <i class="bi bi-plus-circle"/></button>
                </div>
                <div class="d-md-flex">
                    <button type="button" class="btn btn-primary me-2" @click="showFilter"><i class="bi bi-funnel"/>Filter</button>
                    <button type="button" class="btn btn-primary" @click="showSortBy"><i class="bi bi-filter"/>Sort By</button>
                </div>
            </div>
        </div>
        <div class="row" v-if="taskStore.taskLists">
            <div class="col-md-4 mb-3"  v-for="task in taskStore.taskLists.data" :key="task.id">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex">
                            <h5 class="card-title flex-grow-1 pe-2" @click="showTask(task.id)"><a href="#">{{ task.title }}</a></h5>
                            <button class="btn p-0" @click="showTask(task.id)"> <i class="bi bi-pencil-square me-2" /></button>
                            <button class="btn p-0" @click="deleteTask(task.id)"> <i class="bi bi-x-circle" /></button>
                        </div>
                        <div @click="showTask(task.id)">
                            <p class="card-text task-description"> {{ task.description }}</p>
                            <div class="mb-2">
                                <span class="card-text d-block"><small class="text-muted">Status: {{ task.task_status ? task.completed_at ? 'Completed' : 'Incomplete/Todo' : 'No status assigned' }}</small></span>
                                <span class="card-text d-block"><small class="text-muted">Priority: {{ taskIdentity(task.task_priority).status }}</small></span>
                                <span class="card-text d-block"><small class="text-muted">Completed Date: {{ task.completed_at }}</small></span>
                                <span class="card-text d-block"><small class="text-muted">Due Date: {{ task.due_date }}</small></span>
                                <span class="card-text d-block"><small class="text-muted">Created Date: {{ task.created_at }}</small></span>
                            </div>
                        </div>
                        <div class="mb-2 d-md-flex">
                            <button class="btn btn-primary btn-sm me-1" :class="{'disabled' : task.task_status === 1 }" v-if="Number(dataType) !== taskDataTypes.Completed" @click="markAsComplete(task.id)">Mark as Complete</button>
                            <button class="btn btn-primary btn-sm me-1" :class="{'disabled' : task.task_status === 2 }" v-if="Number(dataType) !== taskDataTypes.Todo" @click="markAsInComplete(task.id)">Mark as Incomplete/Todo</button>
                            <button class="btn btn-primary btn-sm" @click="assignArchivedStatus(task)">
                                {{ task.archived_at ? 'Restore' : 'Archived'}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <pagination-items v-if="taskStore.taskLists" :data-type="dataType" @currentPage="setCurrentPage"/>
    </div>
    <!-- Modal -->
    <task-modal :data-type="dataType" ref="taskModalRef"/>
    <filter-modal :data-type="dataType" ref="filterModalRef"/>
    <sort-modal :data-type="dataType" ref="sortModalRef"/>
</template>
<script>
import { computed, inject, onMounted, provide, ref } from 'vue';
import { taskDataTypes } from '@js/components/composables/TaskStatus.js';
import taskStatus from '@js/components/composables/TaskPriority.js';
import TaskModal from './modals/Task.vue';
import FilterModal from './modals/Filter.vue';
import SortModal from './modals/Sort.vue';
import { useTaskStore } from '../stores/TaskStore';

export default {
    props:{
        dataType: {
            type: Number,
            default: 0 // 0 = All, 1 = Archived, 2 = Completed, 3 = Todo
        }
    },
    components: { TaskModal, FilterModal, SortModal },
    setup(props) {
        const currentPage = ref(1);
        const taskStore = useTaskStore();
        const taskModalRef = ref(null);
        const filterModalRef = ref(null);
        const sortModalRef = ref(null);
        const dialog = inject('dialog');
        const { taskIdentity } = taskStatus();

        provide('currentPage', computed(() =>  currentPage.value ));
        // open modal
        const createTask = () => {
            taskModalRef.value.openModal()
        }

        // Show filter
        const showFilter = () => {
           filterModalRef.value.openModal();
        }

        // Show sort
        const showSortBy = () => {
            sortModalRef.value.openModal();
        }

        const showTask = (id) => {
            taskModalRef.value.showDetails(id)
        }

        const deleteTask = async (id) => {
            dialog.show(`Are you sure you want to delete this task?`, {
                title: 'Delete',
                buttons: [
                    {
                        label: 'Proceed',
                        class: 'btn-primary',
                        handler: async (closeModal) => {
                            try{
                                await taskStore.deleteTask(id, {page: currentPage.value, data_type: props.dataType});
                            } catch (e) {
                                closeModal();
                                setTimeout(() => {
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
                                }, 500);
                            }
                            closeModal();
                        }
                    },
                    {
                        label: 'Cancel',
                        class: 'btn-light',
                        handler: (closeModal) => {
                            closeModal();
                        }
                    },
                ],
            });
        }
        
        // Set current page, so user will stay in the same pagination on any actions such as delete, edit
        const setCurrentPage = (page) => {
            currentPage.value = page;
        }

        // Mark task as complete
        const markAsComplete = async(id) => {
            try {
                await taskStore.markAsComplete(id, {page: currentPage.value, data_type: props.dataType});
            } catch (e) {
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
            
        }

        // Mark task as incomplete
        const markAsInComplete = async(id) => {
            try {
                await taskStore.markAsInComplete(id, {page: currentPage.value, data_type: props.dataType});
            } catch (e) {
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
        }

        const assignArchivedStatus = async(task) => {
            try {
                if (!task.archived_at){
                    await taskStore.setAsArchived(task.id, {page: currentPage.value, data_type: props.dataType});
                } else {
                    await taskStore.restoreArchived(task.id, {page: currentPage.value, data_type: props.dataType});
                }
            } catch (e) {
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
        }

        onMounted(() => {
            // taskStore.getTasks({page: currentPage.value, data_type: props.dataType});
        });

        return { 
                taskModalRef, filterModalRef, taskStore, createTask, showTask, deleteTask, showFilter, taskIdentity,
                setCurrentPage, markAsComplete, markAsInComplete, assignArchivedStatus, taskDataTypes, sortModalRef, showSortBy
            }
    },
}
</script>
<style lang="scss" scoped>
    @import '~bootstrap/scss/bootstrap-utilities';
    .card {
        background: #f4f4f4;
        border-color: #ececec;
    }

    .task-description {
        min-height: 3em;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        display: -webkit-box;
        overflow: hidden;
    }

    @include media-breakpoint-down(md) {
        .btn {
            width: 100%;
            margin-bottom: 10px;
        }
    }
</style>