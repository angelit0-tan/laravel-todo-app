<template>
    <!-- Login Form -->
    <div class="container">
      <div class="row justify-content-center mt-5">
        <div class="col-lg-4 col-md-6 col-sm-6">
          <div class="card shadow">
            <div class="card-title text-center border-bottom">
              <h2 class="p-3">Login</h2>
            </div>
            <div class="card-body">
                <div class="mb-4">
                  <label for="email" class="form-label">Email</label>
                  <input-text id="email" name="email" v-model="email" :errors="errors"/>
                </div>
                <div class="mb-4">
                  <label for="password" class="form-label">Password</label>
                  <input-text id="password" name="password" type="password" v-model="password" @keydown.enter="login" :errors="errors"/>
                </div>
                <div class="mb-4" v-if="errors && errors.length">
                  <span class="text-danger fw-bold"> {{ errors }} </span>
                </div>
                <div class="mb-4">
                  <button type="button" class="btn text-light btn-primary w-100" @click="login">Login</button>
                </div>
                <div class="mb-2">
                  Register new <a href="/register">account here</a>
                </div>
            </div>
          </div>
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