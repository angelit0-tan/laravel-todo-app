<template>
    <div 
      class="fixed top-0 left-0 w-full h-full lg:flex items-center justify-center bg-gray-400 bg-opacity-80 overflow-auto" 
      :class="{'invisible': !isShown}">
      <div class="bg-gray-100 rounded p-7">
        <div class="flex justify-between">
          <div class="flex items-center font-black">
              <span>
                <span class="text-2xl font-bold leading-none header">Filter task</span>
              </span>
          </div>
          <button 
              type="button" 
              class="bg-red font-black self-start flex items-center justify-center" 
              @click="hide()"
              style="width: 40px; height: 40px;">
              X
          </button>
        </div>
        <div class="max-w-screen-lg mx-auto">
          <div class="mb-4 flex flex-col gap-1">
              <label for="title">Title</label>
              <input type="text" id="title" v-model="search.title"/>
          </div>
          <div class="mb-4 flex flex-col gap-1">
            <label for="description">Description</label>
            <input type="text" id="description" v-model="search.description"/>
          </div>
          <div class="mb-4 flex flex-col gap-1">
            <label for="description" class="form-label fw-bold">Priority</label>
            <select class="form-select" aria-label="Default select example" v-model="search.priority">
                <option v-for="priority in taskPriority" :key="priority" :value="priority"> 
                    {{ taskIdentity(priority).status }}
                </option>
            </select>
          </div>
          <div class="mb-4" v-if="[taskDataTypes.All, taskDataTypes.Completed].includes(parseInt(dataType))">
              <label for="description" class="form-label fw-bold">Completed date</label>
              <div class="flex gap-4">
                <div class="flex flex-col gap-1">
                    <label for="completed_date_from" class="form-label">From</label>
                    <input type="date" class="form-control" id="completed_date_from" v-model="search.completed_date_from"/>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="completed_date_to" class="form-label">To</label>
                    <input type="date" class="form-control" id="completed_date_to" v-model="search.completed_date_to"/>
                </div>
              </div>
              
          </div>
          <div class="mb-4">
              <label class="form-label fw-bold">Due date</label>
              <div class="flex gap-4">
                <div class="flex flex-col gap-1">
                    <label for="due_date_from" class="form-label">From</label>
                    <input type="date" class="form-control" id="due_date_from" v-model="search.due_date_from"/>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="due_date_to" class="form-label">To</label>
                    <input type="date" class="form-control" id="due_date_to" v-model="search.due_date_to"/>
                </div>
              </div>
          </div>
          <div class="mb-5" v-if="[taskDataTypes.All, taskDataTypes.Archived].includes(parseInt(dataType))">
              <label class="form-label fw-bold">Archive date</label>
              <div class="flex gap-4">
                <div class="flex flex-col gap-1">
                    <label for="archive_date_from" class="form-label">From</label>
                    <input type="date" class="form-control" id="archive_date_from" v-model="search.archive_date_from"/>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="archive_date_to" class="form-label">To</label>
                    <input type="date" class="form-control" id="archive_date_to" v-model="search.archive_date_to"/>
                </div>
              </div>
          </div>
          <div class="mb-3">
            <button type="button" class="bg-blue-500 px-4 py-2 rounded" :disabled="isBusy" @click="searchTasks">Search</button>
          </div>
        </div>
      </div>
    </div>
</template>
<script setup>
  import { inject, onMounted, ref } from 'vue'
  import { useTaskStore } from '@js/stores/TaskStore';
  import { taskDataTypes } from '@js/components/composables/TaskStatus.js';
  import taskStatus from '@js/components/composables/TaskPriority.js';

  const props = defineProps({
    dataType: {
      type: Number,
      default: 0
    }
  })

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

  const isShown = ref(false);

  // Reset fields
  function reset() {
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
  async function searchTasks() {
    isBusy.value = true;
    try {
        await taskStore.getTasks(search.value);
        isBusy.value = false;
    } catch (e) {
        isBusy.value = false;
        // dialog.show(`${e.response?.data?.message || 'Error encountered'}`, {
        //     title: 'Error',
        //     buttons: [
        //         {
        //             label: 'Close',
        //             class: 'btn-light',
        //             handler: (closeModal) => {
        //               hide();
        //             }
        //         },
        //     ],
        // });
    }
    hide();
  }
  
  function show() {
      isShown.value = true
  }

  function hide() {
      isShown.value = false
  }

  defineExpose({
      show,
      hide
  });
</script>