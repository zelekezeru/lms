<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref } from "vue";

const props = defineProps({
  form: Object,
  inventoryCategories: { type: Object, required: true },
  inventorySuppliers: { type: Object, required: true },
});

const emits = defineEmits(["submit"]);

const statusOptions = ref([
  { value: "ACTIVE", label: "Active" },
  { value: "IN_ACTIVE", label: "Inactive" },
]);
</script>

<template>
    <form @submit.prevent="emits('submit')">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Inventory Details -->
            <section class="space-y-6">
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

                <div>
                    <InputLabel for="quantity" value="Quantity" />
                    <TextInput
                        id="quantity"
                        type="number"
                        v-model="form.quantity"
                        required
                        class="w-full"
                    />
                    <InputError :message="form.errors.quantity" />
                </div>

                <div>
                    <InputLabel for="unit_price" value="Unit price (Optional)" />
                    <TextInput
                        id="unit_price"
                        type="number"
                        step="0.01"
                        v-model="form.unit_price"
                        class="w-full"
                    />
                    <InputError :message="form.errors.unit_price" />
                </div>

                <div>
                    <InputLabel for="status" value="Status" />
                    <select
                        id="status"
                        v-model="form.status"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                    >
                        <option disabled value="">Select Status</option>
                        <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                    <InputError :message="form.errors.status" />
                </div>
            </section>

            <!-- Additional Details -->
            <section class="space-y-6">
                <!-- removed tenants for now -->
                <!-- <div>
                    <InputLabel for="tenant_id" value="Tenant (Optional)" />
                    <select
                        id="tenant_id"
                        v-model="form.tenant_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                    >
                        <option disabled value="">Select Tenant</option>
                        <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">
                            {{ tenant.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.tenant_id" />
                </div> -->

                <div>
                    <InputLabel for="inventoryCategory_id" value="InventoryCategory (Optional)" />
                    <select
                        id="inventoryCategory_id"
                        v-model="form.category_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                    >
                        <option disabled value="">Select InventoryCategory</option>
                        <option v-for="inventoryCategory in inventoryCategories" :key="inventoryCategory.id" :value="inventoryCategory.id">
                            {{ inventoryCategory.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.category_id" />
                </div>

                <div>
                    <InputLabel for="inventorySupplier_id" value="InventorySupplier (Optional)" />
                    <select
                        id="inventorySupplier_id"
                        v-model="form.supplier_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                    >
                        <option disabled value="">Select InventorySupplier</option>
                        <option v-for="inventorySupplier in inventorySuppliers" :key="inventorySupplier.id" :value="inventorySupplier.id">
                            {{ inventorySupplier.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.supplier_id" />
                </div>

                <div>
                    <InputLabel for="description" value="Description (Optional)" />
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                    ></textarea>
                    <InputError :message="form.errors.description" />
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
