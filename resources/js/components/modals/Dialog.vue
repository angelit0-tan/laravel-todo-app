<template>
    <div
      ref="modalRef"
      class="modal fade"
      tabindex="-1"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header" v-if="title">
                <h5 class="modal-title">{{ title }}</h5>
                <button type="button" class="btn-close" @click="close"></button>
            </div>
            <div class="modal-body">
              <div v-html="modalContent"/>
            </div>
            <div class="modal-footer">
                <template v-for="(button, index) in buttons" :key="index">
                  <button
                    type="button"
                    class="btn"
                    :class="button.class"
                    @click="button.handler ? button.handler(close) : close()"
                    >{{ button.label }}</button
                  >
                </template>
            </div>
        </div>
      </div>
    </div>
  </template>
  <script>
  import { onMounted, ref } from 'vue'
  import Modal from 'bootstrap/js/dist/modal';
  export default {
      setup() {
          const modalRef = ref(null);
          const modalInstance = ref(null);
          const modalContent = ref(null);
          const title = ref(null);
          const buttons = ref(null);
  
          // open modal
          const prompt = (content, { title : dialogTitle, buttons: dialogButtons }) => {
              modalContent.value = content;
              title.value = dialogTitle;
              buttons.value = dialogButtons;
              modalInstance.value.show();
          }
  
          // close modal
          const close = () => {
            modalInstance.value.hide();
          };
  
          onMounted(() => {
              modalInstance.value = new Modal(modalRef.value, {
                  backdrop: true,
                  keyboard: false,
              });
          });
  
          return { modalRef, modalContent, title, buttons, prompt, close }
      }
  }
  </script>