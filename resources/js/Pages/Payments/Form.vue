<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    form: Object,
    paymentTypes: Array,
    paymentCategories: Array,
    paymentSchedules: Array,
});

const addItem = () => {
    props.form.items.push({ name: "", quantity: 1, unit_price: 0 });
};

const removeItem = (index) => {
    props.form.items.splice(index, 1);
};
</script>

<template>
    <form @submit.prevent="$emit('submit')">
        <div class="mb-4">
            <label for="payment_type_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                Payment Type
            </label>
            <select
                v-model="form.payment_type_id"
                id="payment_type_id"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
            >
                <option value="" disabled>Select Payment Type</option>
                <option :value="type.id" v-for="type in paymentTypes" :key="type.id">{{ type.name }}</option>
            </select>
            <div v-if="form.errors.payment_type_id" class="text-red-500 text-sm">{{ form.errors.payment_type_id }}</div>
        </div>

        <div class="mb-4">
            <label for="payment_category_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                Payment Category
            </label>
            <select
                v-model="form.payment_category_id"
                id="payment_category_id"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
            >
                <option :value="null">-- Select Category (Optional) --</option>
                <option :value="category.id" v-for="category in paymentCategories" :key="category.id">{{ category.name }}</option>
            </select>
            <div v-if="form.errors.payment_category_id" class="text-red-500 text-sm">{{ form.errors.payment_category_id }}</div>
        </div>

        <div class="mb-4">
            <label for="payment_schedule_item_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                Payment Schedule Item (Optional)
            </label>
            <select
                v-model="form.payment_schedule_item_id"
                id="payment_schedule_item_id"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
            >
                <option :value="null">-- Select Schedule Item (Optional) --</option>
                <optgroup v-for="schedule in paymentSchedules" :key="schedule.id" :label="schedule.description">
                    <option v-for="item in schedule.items" :key="item.id" :value="item.id">
                        {{ item.name }} (Due: {{ item.due_date }}, Remaining: {{ item.expected_amount - item.paid_amount }})
                    </option>
                </optgroup>
            </select>
            <div v-if="form.errors.payment_schedule_item_id" class="text-red-500 text-sm">{{ form.errors.payment_schedule_item_id }}</div>
            <small class="text-gray-600 dark:text-gray-400">Link this payment to a specific installment if applicable.</small>
        </div>

        <div class="mb-4">
            <label for="payment_date" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                Payment Date
            </label>
            <input
                type="date"
                id="payment_date"
                v-model="form.payment_date"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
            />
            <div v-if="form.errors.payment_date" class="text-red-500 text-sm">{{ form.errors.payment_date }}</div>
        </div>

        <div class="mb-4">
            <label for="total_amount" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                Total Amount
            </label>
            <input
                type="number"
                id="total_amount"
                v-model="form.total_amount"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
            />
            <div v-if="form.errors.total_amount" class="text-red-500 text-sm">{{ form.errors.total_amount }}</div>
        </div>

        <div class="mb-4">
            <label for="payment_method" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                Payment Method
            </label>
            <input
                type="text"
                id="payment_method"
                v-model="form.payment_method"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
            />
            <div v-if="form.errors.payment_method" class="text-red-500 text-sm">{{ form.errors.payment_method }}</div>
        </div>

        <div class="mb-4">
            <label for="status" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                Status
            </label>
            <select
                v-model="form.status"
                id="status"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
            >
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="failed">Failed</option>
                <option value="refunded">Refunded</option>
            </select>
            <div v-if="form.errors.status" class="text-red-500 text-sm">{{ form.errors.status }}</div>
        </div>

        <div class="mb-4">
            <label for="payment_reference" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                Payment Reference
            </label>
            <input
                type="text"
                id="payment_reference"
                v-model="form.payment_reference"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
            />
            <div v-if="form.errors.payment_reference" class="text-red-500 text-sm">{{ form.errors.payment_reference }}</div>
        </div>

        <div>
            <h3>Payment Items</h3>
            <div v-for="(item, index) in form.items" :key="index" class="mb-4 border-b pb-4">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label :for="'item_name_' + index" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Name</label>
                        <input type="text" :id="'item_name_' + index" v-model="form.items[index].name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                        <div v-if="form.errors[`items.${index}.name`]" class="text-red-500 text-sm">{{ form.errors[`items.${index}.name`] }}</div>
                    </div>
                    <div>
                        <label :for="'item_quantity_' + index" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Quantity</label>
                        <input type="number" :id="'item_quantity_' + index" v-model="form.items[index].quantity" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                        <div v-if="form.errors[`items.${index}.quantity`]" class="text-red-500 text-sm">{{ form.errors[`items.${index}.quantity`] }}</div>
                    </div>
                    <div>
                        <label :for="'item_unit_price_' + index" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Unit Price</label>
                        <input type="number" :id="'item_unit_price_' + index" v-model="form.items[index].unit_price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                        <div v-if="form.errors[`items.${index}.unit_price`]" class="text-red-500 text-sm">{{ form.errors[`items.${index}.unit_price`] }}</div>
                    </div>
                </div>
                <button type="button" @click="removeItem(index)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-2">Remove Item</button>
            </div>
            <button type="button" @click="addItem" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-4">Add Item</button>
            <div v-if="form.errors.items" class="text-red-500 text-sm">{{ form.errors.items }}</div>
        </div>

        <div class="mt-6">
            <button
                type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                :disabled="form.processing"
            >
                Record Payment
            </button>
            <Link :href="route('students.show', form.student_id)" class="inline-block ml-2 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                Cancel
            </Link>
        </div>
    </form>
</template>