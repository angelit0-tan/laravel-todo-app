<template>
    <div>
        <nav>
            <ul class="pagination">
                <li class="page-item" :class="{ 'disabled' : !taskStore.taskLists.links.prev }">
                    <button class="page-link" @click="goToPrevNext(taskStore.taskLists.links.prev)"> Previous</button>
                </li>
                <li v-for="n in taskStore.taskLists.meta.last_page" class="page-item" :key="n">
                    <button class="page-link " :class="{ 'active' : currentPage == n }" @click="gotoPage(n)"> {{ n }} </button>
                </li>
                <li class="page-item" :class="{ 'disabled' : !taskStore.taskLists.links.next }">
                    <button class="page-link" @click="goToPrevNext(taskStore.taskLists.links.next)"> Next </button>
                </li>
            </ul>
        </nav>
    </div>
</template>
<script>
import { ref } from 'vue';
import { useTaskStore } from '../Stores/TaskStore';
export default {
    props:{
        dataType: {
            type: Number,
            default: 0
        }
    },
    emit: ['currentPage'],
    setup(props, { emit }) {

        const currentPage = ref(1);
        const taskStore = useTaskStore();

        // for pagination, get current page
        const gotoPage = (page) => {
            currentPage.value = page;
            taskStore.getTasks({page, data_type: props.dataType});
            emit('currentPage', currentPage.value);

        }

        // get current page
        const goToPrevNext = async (link) => {
            await taskStore.goToPrevNext(link, { data_type: props.dataType});
            currentPage.value = taskStore.taskLists.meta.current_page;
            emit('currentPage', currentPage.value);
        }

        return { currentPage, taskStore, gotoPage, goToPrevNext }
    },
}
</script>