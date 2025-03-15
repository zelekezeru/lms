<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    form: Object,
});

const emits = defineEmits(["submit"]);
</script>

<template>
    <form @submit.prevent="emits('submit')">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- User Details -->
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    class="w-full"
                />
                <InputError :message="form.errors.name" />
            </div>
        </div>

        <div class="mt-6 flex justify-center">
            <PrimaryButton :disabled="form.processing">
                <span v-if="!form.processing">Submit</span>
                <span v-else>Submitting...</span>
            </PrimaryButton>
        </div>
    </form>
</template>
