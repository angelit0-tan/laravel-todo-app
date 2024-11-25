<template>
  <div 
    class="fixed top-0 left-0 w-full h-full flex items-center justify-center bg-white bg-opacity-50 zindex" 
    :class="{'invisible': !isShown}">
    <div class="bg-light-gray wrapper" style="max-width: 560px">
      <div class="flex justify-between">
        <div class="flex items-center font-black px-10 pt-10">
            <font-awesome icon="circle" class="text-base mr-4" />
            <span>
              <span class="text-2xl font-bold leading-none header">Activation Success!</span>
            </span>
        </div>
        <button 
            type="button" 
            class="bg-red font-black self-start flex items-center justify-center" 
            @click="hide()"
            style="width: 40px; height: 40px;">
            <font-awesome icon="xmark" class="text-lg text-white"  />
        </button>
      </div>
      <div class="max-w-screen-lg mx-auto p-10">
        <p class="mb-5">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
        </p>
      </div>
    </div>
  </div>
</template>
<script setup>
  const isShown = ref(false);
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