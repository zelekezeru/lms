<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import SelectInput from "@/Components/SelectInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    form: {
        type: Object,
        default: () => ({
            name: "",
            description: "",
            amount: null,
            type: null,
            payment_method: null,
            payment_status: null,
            installment_count: null,
            installment_interval: null,
            grace_period: null,
            late_fee: null,
            late_fee_amount: null,
            discount: null,
            discount_amount: null,
            penalty: null,
            penalty_amount: null,
            status: null,
            payment_schedule_id: null,
            payment_category_id: null,
            user_id: null,
            total_amount: null,
            errors: {},
        }),
    },
    users: {
        type: Object,
        default: () => [],
    },
    paymentCategories: {
        type: Object,
        default: () => [],
    },
    paymentSchedules: {
        type: Object,
        default: () => [],
    },
});

const paymentMethods = [
    { id: 1, name: "Cash" },
    { id: 2, name: "Bank Transfer" },
    { id: 3, name: "Cheque" },
    { id: 4, name: "Credit Card" },
];

const paymentStatuses = [
    { id: 1, name: "Pending" },
    { id: 2, name: "Completed" },
    { id: 3, name: "Failed" },
    { id: 4, name: "Cancelled" },
];

const paymentTypes = [
    { id: 1, name: "Fixed" },
    { id: 2, name: "Percentage" },
];

const statuses = [
    { id: 1, name: "Active" },
    { id: 2, name: "Inactive" },
];

</script>

<template>
    <form @submit.prevent="$emit('submit')" class="space-y-6">
        <div>
            <InputLabel for="name" value="Name" />
            <TextInput id="name" v-model="form.name" required class="mt-1" />
            <InputError :message="form.errors.name" class="mt-2" />
        </div>

        <div>
            <InputLabel for="description" value="Description" />
            <TextInput id="description" v-model="form.description" class="mt-1" />
            <InputError :message="form.errors.description" class="mt-2" />
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <InputLabel for="amount" value="Amount" />
                <TextInput id="amount" type="number" v-model="form.amount" required class="mt-1" />
                <InputError :message="form.errors.amount" class="mt-2" />
            </div>
            <div>
                <InputLabel for="type" value="Type" />
                <SelectInput
                    id="type"
                    :modelValue="form.type"
                    @update:modelValue="form.type = $event"
                    :options="paymentTypes"
                    labelKey="name"
                    valueKey="id"
                    required
                    class="mt-1"
                />
                <InputError :message="form.errors.type" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <InputLabel for="payment_method" value="Payment Method" />
                <SelectInput
                    id="payment_method"
                    :modelValue="form.payment_method"
                    @update:modelValue="form.payment_method = $event"
                    :options="paymentMethods"
                    labelKey="name"
                    valueKey="id"
                    required
                    class="mt-1"
                />
                <InputError :message="form.errors.payment_method" class="mt-2" />
            </div>
            <div>
                <InputLabel for="payment_status" value="Payment Status" />
                <SelectInput
                    id="payment_status"
                    :modelValue="form.payment_status"
                    @update:modelValue="form.payment_status = $event"
                    :options="paymentStatuses"
                    labelKey="name"
                    valueKey="id"
                    required
                    class="mt-1"
                />
                <InputError :message="form.errors.payment_status" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <InputLabel for="installment_count" value="Installment Count" />
                <TextInput id="installment_count" type="number" v-model="form.installment_count"  class="mt-1" />
                <InputError :message="form.errors.installment_count" class="mt-2" />
            </div>
            <div>
                <InputLabel for="installment_interval" value="Installment Interval (days)" />
                <TextInput id="installment_interval" type="number" v-model="form.installment_interval"  class="mt-1" />
                <InputError :message="form.errors.installment_interval" class="mt-2" />
            </div>
        </div>

        <div>
            <InputLabel for="grace_period" value="Grace Period (days)" />
            <TextInput id="grace_period" type="number" v-model="form.grace_period"  class="mt-1" />
            <InputError :message="form.errors.grace_period" class="mt-2" />
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <InputLabel for="late_fee" value="Late Fee (%)" />
                <TextInput id="late_fee" type="number" v-model="form.late_fee"  class="mt-1" />
                <InputError :message="form.errors.late_fee" class="mt-2" />
            </div>
            <div>
                <InputLabel for="late_fee_amount" value="Late Fee Amount" />
                <TextInput id="late_fee_amount" type="number" v-model="form.late_fee_amount"  class="mt-1" />
                <InputError :message="form.errors.late_fee_amount" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <InputLabel for="discount" value="Discount (%)" />
                <TextInput id="discount" type="number" v-model="form.discount"  class="mt-1" />
                <InputError :message="form.errors.discount" class="mt-2" />
            </div>
            <div>
                <InputLabel for="discount_amount" value="Discount Amount" />
                <TextInput id="discount_amount" type="number" v-model="form.discount_amount"  class="mt-1" />
                <InputError :message="form.errors.discount_amount" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <InputLabel for="penalty" value="Penalty (%)" />
                <TextInput id="penalty" type="number" v-model="form.penalty"  class="mt-1" />
                <InputError :message="form.errors.penalty" class="mt-2" />
            </div>
            <div>
                <InputLabel for="penalty_amount" value="Penalty Amount" />
                <TextInput id="penalty_amount" type="number" v-model="form.penalty_amount"  class="mt-1" />
                <InputError :message="form.errors.penalty_amount" class="mt-2" />
            </div>
        </div>

        <div>
            <InputLabel for="status" value="Status" />
            <SelectInput id="status" v-model="form.status" :options="statuses"  labelKey="name" valueKey="id" required class="mt-1" />
            <InputError :message="form.errors.status" class="mt-2" />
        </div>

        <div>
            <InputLabel for="payment_schedule_id" value="Payment Schedule" />
            <SelectInput
                id="payment_schedule_id"
                :modelValue="form.payment_schedule_id"
                @update:modelValue="form.payment_schedule_id = $event"
                :options="Object.entries(paymentSchedules).map(([key, value]) => ({ value: key, label: value }))"
                class="mt-1"
            />
            <InputError :message="form.errors.payment_schedule_id" class="mt-2" />
        </div>

        <div>
            <InputLabel for="payment_category_id" value="Payment Category" />
            <SelectInput
                id="payment_category_id"
                :modelValue="form.payment_category_id"
                @update:modelValue="form.payment_category_id = $event"
                :options="Object.entries(paymentCategories).map(([key, value]) => ({ value: key, label: value }))"
                class="mt-1"
            />
            <InputError :message="form.errors.payment_category_id" class="mt-2" />
        </div>

        <div>
            <InputLabel for="user_id" value="User" />
            <SelectInput
                id="user_id"
                :modelValue="form.user_id"
                @update:modelValue="form.user_id = $event"
                :options="Object.entries(users).map(([key, value]) => ({ value: key, label: value }))"
                required
                class="mt-1"
            />
            <InputError :message="form.errors.user_id" class="mt-2" />
        </div>

        <div>
            <InputLabel for="total_amount" value="Total Amount" />
            <TextInput id="total_amount" type="number" v-model="form.total_amount" required class="mt-1" />
            <InputError :message="form.errors.total_amount" class="mt-2" />
        </div>

        <PrimaryButton :disabled="form.processing">
            Create Payment
        </PrimaryButton>
        <Link :href="route('payments.index')" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 ml-2">
            Cancel
        </Link>
    </form>
</template>
