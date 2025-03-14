<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { PhotoIcon } from "@heroicons/vue/24/outline";

defineProps({
});

// Initialize form data
const form = useForm({
    name: "",
    email: "",
    code: "",
    phone: "",
    address: "",
    contact_person: "",
    contact_phone: "",
    addrement: "",
    aggrement_image: null,
    password: "aez@tenant",
    password_confirmation: "aez@tenant",
});

// Ref to hold the image preview
const imagePreview = ref(null);

// Handle logoge selection and preview
const handleFileChange = (e) => {
    const file = e.target.files[0];
    form.logo = file; // Assign file to form
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        imagePreview.value = null;
    }
};

// Submit the form
const submit = () => {
    form.post(route("tenants.store"));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Register Tenant
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Fill in the details of a system Tenant.
                </p>
            </div>

            <div class="dark:bg-gray-900 bg-white-100 shadow-lg rounded-lg p-6">
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Tenant Details -->
                        <section class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Institution Name" />
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
                                <InputLabel for="password" value="Password" />
                                <TextInput
                                    id="password"
                                    type="text"
                                    v-model="form.password"
                                    value="aez@tenant (Default Password)"
                                    readonly
                                    class="w-full bg-gray-200"
                                />
                                <InputError :message="form.errors.password" />
                            </div>
                            
                            <div>
                                <InputLabel for="phone" value="Institution Phone" />
                                <TextInput
                                    id="phone"
                                    type="text"
                                    v-model="form.phone"
                                    required
                                    class="w-full"
                                />
                                <InputError :message="form.errors.phone" />
                            </div>
                            
                            <div>
                                <InputLabel for="address" value="Institution Address" />
                                <TextInput
                                    id="address"
                                    type="text"
                                    v-model="form.address"
                                    required
                                    class="w-full"
                                />
                                <InputError :message="form.errors.address" />
                            </div>

                            <!-- Institution Logo -->
                            <div>
                                <InputLabel
                                    for="logo"
                                    value="Institution Logo"
                                />
                                <div class="flex items-center gap-4">
                                    <label
                                        for="logo"
                                        class="cursor-pointer px-4 py-2 text-white flex items-center gap-2 rounded-md shadow transition bg-black hover:bg-blue-700"
                                    >
                                        <PhotoIcon class="w-5 h-5" />
                                        Upload Logo
                                    </label>

                                    <input
                                        id="logo"
                                        type="file"
                                        accept="image/*"
                                        class="hidden"
                                        @change="handleFileChange"
                                    />

                                    <!-- Image Preview -->
                                    <div
                                        v-if="imagePreview"
                                        class="w-16 h-16 rounded-full border shadow overflow-hidden"
                                    >
                                        <img
                                            :src="imagePreview"
                                            alt="Logo Preview"
                                            class="object-cover w-full h-full"
                                        />
                                    </div>
                                </div>
                                <InputError
                                    :message="form.errors.logo"
                                />
                            </div>
                            
                            <div>
                                <InputLabel for="contact_person" value="Representative's Name" />
                                <TextInput
                                    id="contact_person"
                                    type="text"
                                    v-model="form.contact_person"
                                    required
                                    class="w-full"
                                />
                                <InputError :message="form.errors.contact_person" />
                            </div>
                            
                            <div>
                                <InputLabel for="contact_phone" value="Representative's Phone" />
                                <TextInput
                                    id="contact_phone"
                                    type="text"
                                    v-model="form.contact_phone"
                                    required
                                    class="w-full"
                                />
                                <InputError :message="form.errors.contact_phone" />
                            </div>

                        </section>

                            <!-- Profile Image Upload & Preview -->
                            <div>
                                <InputLabel
                                    for="profile_img"
                                    value="Profile Image"
                                />
                                <div class="flex items-center gap-4">
                                    <label
                                        for="profile_img"
                                        class="cursor-pointer px-4 py-2 text-white flex items-center gap-2 rounded-md shadow transition bg-black hover:bg-blue-700"
                                    >
                                        <PhotoIcon class="w-5 h-5" />
                                        Upload Image
                                    </label>

                                    <input
                                        id="profile_img"
                                        type="file"
                                        accept="image/*"
                                        class="hidden"
                                        @change="handleFileChange"
                                    />

                                    <!-- Image Preview -->
                                    <div
                                        v-if="imagePreview"
                                        class="w-16 h-16 rounded-full border shadow overflow-hidden"
                                    >
                                        <img
                                            :src="imagePreview"
                                            alt="Profile Preview"
                                            class="object-cover w-full h-full"
                                        />
                                    </div>
                                </div>
                                <InputError
                                    :message="form.errors.profile_img"
                                />
                            </div>


                    </div>

                    <div class="mt-6 flex justify-center">
                        <PrimaryButton :disabled="form.processing">
                            <span v-if="!form.processing">Submit</span>
                            <span v-else>Submitting...</span>
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Smooth fade-in effect for image preview */
img {
    transition: opacity 0.3s ease-in-out;
}
</style>