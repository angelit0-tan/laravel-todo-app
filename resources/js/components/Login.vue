<template>
    <div class="max-w-sm mx-auto px-3 lg:px-0 py-10">
      <div class="w-full bg-white rounded shadow-lg p-8 m-4 md:max-w-sm md:mx-auto">
          <span class="block w-full text-xl uppercase font-bold mb-4">Login</span>      
          <div class="mb-4 md:w-full">
            <label for="email" class="block text-xs mb-1">Username or Email</label>
            <input-text id="email" name="email" v-model="email" :errors="errors"/>
          </div>
          <div class="mb-6 md:w-full">
            <label for="password" class="block text-xs mb-1">Password</label>
            <input-text id="password" name="password" type="password" v-model="password" @keydown.enter="login" :errors="errors"/>
          </div>
          <div class="mb-4" v-if="errors && errors.length">
            <span class="text-red font-bold"> {{ errors }} </span>
          </div>
          <div class="font-bold"> 
            <button class="bg-green-500 hover:bg-green-700 text-white uppercase font-semibold px-4 py-2 rounded" @click="login">Login</button>
            Register new <a class="text-blue-700 text-center" href="/register">account here</a>
          </div>
      </div>
    </div>
  </template>
  <script>
  import http from '@js/http.js';
  import { ref } from 'vue';
  
  export default {
    setup() {
      const errors = ref(null);
      const email = ref(null);
      const password = ref(null);
  
      const login = async() => {
        try{
            await http.post("login", {email: email.value, password: password.value });
            window.location.href = "home";
        } catch (e) {
          errors.value = e.response.data?.message;
        }
      }
  
      const task = async() => {
        await http.get("/api/tasks");
      }
  
      return { errors, email, password, login, task }
    }
  }
  </script>