<template>
    <div class="max-w-sm mx-auto px-3 lg:px-0 py-10">
        <div class="w-full bg-white rounded shadow-lg p-8 m-4 md:max-w-sm md:mx-auto">
            <span class="block w-full text-xl uppercase font-bold mb-4">Register</span>      
            <div class="mb-4">
                <label for="email" class="form-label">Email</label>
                <input-text id="email" name="email" v-model="user.email" :errors="errors"/>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input-text id="password" name="password" v-model="user.password" type="password" :errors="errors"/>
            </div>
            <div class="mb-4">
                <label for="confirm_password" class="form-label">Confirm password</label>
                <input-text id="confirm_password" name="confirm_password" v-model="user.password_confirmation" type="password" :errors="errors"/>
            </div>
            <div class="mb-4">
                <button type="submit" class="bg-indigo-800 hover:bg-indigo-300 text-white uppercase font-semibold px-4 py-2 rounded" @click="register">Register</button>
            </div>
            <div class="mb-2">
                Already had account? <a class="text-blue-700 text-center" href="/">Login here</a>
            </div>
        </div>
    </div>
  </template>
  <script>
      import { ref } from 'vue';
      import http from '@js/http.js';
      export default {
          setup() {
              const errors = ref(null);
              const user = ref({
                  email: null,
                  password: null,
                  password_confirmation: null
              })
  
              // Method
              const register = async() => {
                errors.value = null;
                try {
                  await http.post("register", user.value);
                  window.location.href = "home";
                } catch (e) {
                  errors.value = e.response.data?.message;
                }
              }
  
              return { user, errors, register }
          }
      }
  </script>