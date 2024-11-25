<template>
  <div 
    class="fixed top-0 left-0 w-full h-full lg:flex items-center justify-center bg-gray-400 bg-opacity-80 overflow-auto" 
    :class="{'invisible': !isShown}">
    <div class="bg-gray-100 rounded p-7">
      <div class="flex justify-between mb-5">
        <div class="flex items-center font-black">
            <span>
              <span class="text-2xl font-bold leading-none header">Task</span>
            </span>
        </div>
        <button 
            type="button" 
            class="font-black self-start flex items-center justify-center" 
            @click="hide()"
            style="width: 40px; height: 40px;">
            X
        </button>
      </div>
      <div class="max-w-screen-lg mx-auto">
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
        <div class="mb-8">
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
        <div class="flex gap-2">
            <button type="button" class="bg-green-500 px-4 py-2 rounded" :disabled="isBusy" @click="saveTask">Save</button>
            <button type="button" class="bg-red-500 px-4 py-2 rounded" @click="hide">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>

  import { inject, ref } from 'vue';
  import Attachment from '@js/components/inputs/Attachment.vue';
  import http from '@js/http.js';
  import { useTaskStore } from '@js/stores/TaskStore';
  import taskStatus from '@js/components/composables/TaskPriority.js';
  import { fileTypes, isValidFile } from '@js/components/composables/FileType.js';

  const props = defineProps({
    dataType: {
      type: Number,
      default: 0
    }
  })

  const isShown = ref(false);
  const taskStore = useTaskStore();
  const errors = ref(null);
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
  async function addTags() {
      // Check with value and no duplicates
      if (tag.value.trim() && !task.value.tags?.map((i) => i.name).includes(tag.value)) {
          const data = await taskStore.addTag({ 'name': tag.value });
          task.value.tags.push(data);
      }
      tag.value = null;
  }

  // Remove tag
  function removeTag (id) {
    task.value.tags = task.value.tags.filter((i) => i.id !== id);
  }

    // form reset
  function reset() {
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

  // Add task
  async function saveTask() {
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
        hide();
        reset();
    } catch (e) {
        errors.value = e.response?.data?.message;
        isBusy.value = false;
    }
  }

  // Display task details
  async function showDetails(id) {
    const data = await taskStore.getTask(id);
    task.value = data;
    show();
  }

  // Upload of task files
  async function attachFiles(event) {
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
  function updateAttachment(attachmentId, newData) {
    const index = task.value.attachments.findIndex((i) =>i.id === attachmentId);
    task.value.attachments[index] = newData;
  }

  // Remove attachment
  function removeAttachment(attachmentId) {
    task.value.attachments = task.value.attachments.filter((i) => i.id !== attachmentId);
  }
  
  // // Mounted
  // onMounted(() => {
  //     myModal.value = new Modal(createTaskModalRef.value, {
  //         keyboard: false
  //     });
  // });

  // open modal
  function show() {
    isShown.value = true
  }

  // Close modal
  function hide() {
    reset();
    isShown.value = false
  }

  defineExpose({
      show,
      hide,
      showDetails
  });
</script>