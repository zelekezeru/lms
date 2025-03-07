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
                <div class="flex justify-center items-center min-h-screen py-12" style="padding-top: 1rem; padding-left: 16rem;"> <!-- Adjust padding to avoid overlapping -->
                    
                    <div class="pt-4 px-4">
                        <slot name="header" />
                    </div>

                    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">

                                <div class="flex justify-center">
                                    <div class="w-full">
                                        <slot />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
/* No additional styles needed as Tailwind CSS is used */
</style>
