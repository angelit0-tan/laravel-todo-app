<template>
    <header class="header">
      <nav class="navbar navbar-expand-md bg-white">
        <div class="container-fluid">
          <a class="navbar-brand" href="#">TODO App</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse d-md-flex justify-content-md-end" id="navbarCollapse">
            <ul class="navbar-nav mb-2 mb-md-0">
              <li class="nav-item">
                <a class="nav-link" :class="{'active' : page === 'home'}" aria-current="page" href="/home">My Tasks</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" :class="{'active' : page === 'completed'}" aria-current="page" href="/completed-tasks">Completed Tasks</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" :class="{'active' : page === 'todo'}" aria-current="page" href="/todo-tasks">Todo Tasks</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" :class="{'active' : page === 'archived'}" aria-current="page" href="/archived-tasks">Archived Tasks</a>
              </li>
              <li class="nav-item">
                <button class="btn nav-link" @click="logout">Logout</button>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
</template>
<script>
import http from '@js/http.js';
export default {
  props:{
    page: {
      type: String,
      default: 'home'
    }
  },
  setup() {
      const logout = async() => {
        await http.post('/logout');
        window.location.href = "/";
      }

      return { logout }
  },
}
</script>
<style scoped>
  .active {
    font-weight: 800;
    color: rgb(6, 40, 230)!important;
  }
</style>