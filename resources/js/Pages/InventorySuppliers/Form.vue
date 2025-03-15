<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
  form: Object
})

const emits = defineEmits(['submit']);

</script>

<template>
    <form @submit.prevent="emits('submit')">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- User Details -->
            <section class="space-y-6">
                <div>
                    <InputLabel for="name" value="Full Name" />
                    <TextInput
                        id="name"
                        type="text"
                        v-model="form.name"
                        required
                        class="w-full"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        class="w-full"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="contact" value="Contact" />
                    <TextInput
                        id="contact"
                        type="text"
                        v-model="form.contact"
                        required
                        class="w-full"
                    />
                    <InputError :message="form.errors.contact" />
                </div>
            </section>

            <!-- Additional Details -->
            <section class="space-y-6">
                <div>
                    <InputLabel for="tenant_id" value="Tenant" />
                    <select
                        id="tenant_id"
                        v-model="form.tenant_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                    >
                        <option disabled value="">Select Tenant</option>
                        <option
                            v-for="tenant in tenants"
                            :key="tenant.id"
                            :value="tenant.id"
                        >
                            {{ tenant.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.tenant_id" />
                </div>

                <div>
                    <InputLabel for="address" value="Address" />
                    <TextInput
                        id="address"
                        type="text"
                        v-model="form.address"
                        required
                        class="w-full"
                    />
                    <InputError :message="form.errors.address" />
                </div>
            </section>
        </div>

        <div class="mt-6 flex justify-center">
            <PrimaryButton :disabled="form.processing">
                <span v-if="!form.processing">Submit</span>
                <span v-else>Submitting...</span>
            </PrimaryButton>
        </div>
    </form>
</template>
