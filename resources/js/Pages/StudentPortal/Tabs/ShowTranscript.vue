<script setup>
import { ref } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Modal from "@/Components/Modal.vue";

const showTranscriptsModal = ref(false);

const closeModal = () => {
    showTranscriptsModal.value = false;
};
</script>

<template>
    <!-- Transcripts Page -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-300 dark:border-gray-600">
        <h2 class="text-xl font-bold mb-4">Transcripts</h2>
        <p>Transcript details and download options will be shown here.</p>
        <div class="mt-4 flex justify-end">
            <PrimaryButton @click="showTranscriptsModal = true">View Transcript Modal</PrimaryButton>
        </div>
    </div>
    <!-- Transcripts Modal -->
    <Modal :show="showTranscriptsModal" @close="closeModal" :max-width="'lg'">
        <div class="p-6">
            <h2 class="text-xl font-bold mb-4">Transcript Details</h2>
            <p>Transcript details and actions go here.</p>
            <div class="mt-4 flex justify-end">
                <PrimaryButton @click="closeModal">Close</PrimaryButton>
            </div>
        </div>
    </Modal>
</template>