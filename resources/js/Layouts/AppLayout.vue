<template>
    <div>
        <Navigation />
        <Sidebar />
        <main>
            <slot />
        </main>
    </div>
</template>

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