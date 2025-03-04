<script>
import Navigation from '@/Components/Navigation.vue';
import Sidebar from '@/Components/Sidebar.vue';

export default {
    components: {
        Navigation,
        Sidebar,
    },
}
</script>


<template>
    <div class="wrapper flex h-screen">
        <Sidebar />
        <div class="flex flex-col flex-1">
            <div class="flex justify-end">
                <Navigation />
            </div>
            <main class="flex-1 p-4 overflow-y-auto">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
/* No additional styles needed as Tailwind CSS is used */
</style>
