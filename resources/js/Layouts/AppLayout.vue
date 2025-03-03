<script>
import Navigation from '@/Components/Navigation.vue';
import Sidebar from '@/Components/Sidebar.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        Navigation,
        Sidebar,
    },
}
</script>


<template>
    <div class="wrapper">
        <Navigation />
        <Sidebar />
        <main>
            <slot />
        </main>
    </div>
</template>
