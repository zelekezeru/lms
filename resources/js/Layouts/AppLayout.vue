<script>
import Navigation from '@/Components/Navigation.vue';
import Sidebar from '@/Components/Sidebar.vue';
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        Navigation,
        Sidebar,
    },
}
</script>


<template>
    <div>
        <Navigation />
        <Sidebar />
        <main>
            <slot />
        </main>
    </div>
</template>
