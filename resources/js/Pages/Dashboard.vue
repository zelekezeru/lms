<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<script>
export default {
    methods: {
        logout() {
            this.$inertia.post(route('logout'));
        }
    }
}
</script>
<template>
    <Head title="Dashboard" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>
        
        <form @submit.prevent="logout">
            <button type="submit" class="dropdown-item">
                Log Out
            </button>
        </form>
            <div class="flex flex-wrap">
                <!-- <a href="{{ route('tasks.index') }}"> -->
                    <div class="w-full sm:w-1/4 p-2">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="flex items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                                            <i class="fa-solid fa-list-check"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3">
                                        <div class="numbers">
                                            <p class="card-category">Card 1</p>
                                            <h4 class="card-title">10</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!-- </a> -->
                </div>
                <div class="w-full sm:w-1/4 p-2">
                    <!-- <a href="{{ route('tasks.list', 'Pending') }}"> -->
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="flex items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-danger bubble-shadow-small">
                                            <i class="fa-solid fa-spinner"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3">
                                        <div class="numbers">
                                            <p class="card-category">Card 2</p>
                                            <h4 class="card-title">20</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!-- </a> -->
                </div>
                <div class="w-full sm:w-1/4 p-2">
                    <!-- <a href="{{ route('tasks.list', 'Progress') }}"> -->
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="flex items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-warning bubble-shadow-small">
                                            <i class="fa-solid fa-arrow-up-right-dots"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3">
                                        <div class="numbers">
                                            <p class="card-category">Card 3</p>
                                            <h4 class="card-title">30</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!-- </a> -->
                </div>
                <div class="w-full sm:w-1/4 p-2">
                    <!-- <a href="{{ route('tasks.list', 'Completed') }}"> -->
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="flex items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-success bubble-shadow-small">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3">
                                        <div class="numbers">
                                            <p class="card-category">Card 4</p>
                                            <h4 class="card-title">40</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!-- </a> -->
                </div>
            </div>
    </AppLayout>
</template>
