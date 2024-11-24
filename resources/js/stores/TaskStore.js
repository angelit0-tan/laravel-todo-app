import { defineStore } from "pinia";
import http from '@js/http.js';

export const useTaskStore = defineStore('task', {
    //States
    state: () => ({
        tasks: null
    }),

    // Getters
    getters: {
        taskLists() {
            return this.tasks;
        }
    },

    // Actions
    actions: {
        // Get all tasks
        async getTasks(params = {page: 1}) {
            alert('task')
            const { data } = await http.get(`api/tasks`, {params});
            this.tasks = data;
        },

        // Add new tag
        async addTask(params) {
            await http.post('api/tasks', params);
        },

        // get single task detetails
        async getTask(id) {
           const { data } = await http.get(`api/tasks/${id}`);
           return data;
        },

        // Add tag
        async addTag(params) {
            const { data } = await http.post('api/tags', params);
            return data;
        },

        // Update a task
        async updateTask(params) {
            await http.patch(`api/tasks/${params.id}`, params);
        },

        // Delete task
        async deleteTask(id, params) {
            await http.delete(`api/tasks/${id}`);
            this.getTasks(params);
        },

        // For pagination prev and next link
        async goToPrevNext(link, params = { data_type: 0}) {
            const { data } = await http.get(`${link}`, { params });
            this.tasks = data;
        },

        // Mark task as complete
        async markAsComplete(id, params) {
            await http.patch(`api/tasks/${id}/complete`);
            this.getTasks(params);
        },

        // Mark task as incomplete
        async markAsInComplete(id, params) {
            await http.patch(`api/tasks/${id}/incomplete`);
            this.getTasks(params);
        },

        // Set archived
        async setAsArchived(id, params) {
            await http.patch(`api/tasks/${id}/archive`);
            this.getTasks(params);
        },

        // Restore archive
        async restoreArchived(id, params) {
            await http.patch(`api/tasks/${id}/restore`);
            this.getTasks(params);
        }
    }
});