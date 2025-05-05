<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";
import { EyeIcon, TrashIcon } from "@heroicons/vue/24/solid";
import { PencilSquareIcon } from "@heroicons/vue/24/outline";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "@/Components/TableZebraRows.vue";
import Thead from "@/Components/Thead.vue"; // Adjusted path for Thead

defineProps({
    payments: {
        type: Object,
        required: true,
    },
    sortInfo: {
        type: Object,
        required: false,
    },
    deletePayment: {
        type: Function,
        required: true,
    },
    search: {
        type: String,
        required: false,
    },
});
</script>

<template>
    <div class="overflow-x-auto shadow-md sm:rounded-lg mt-3">
        <Table>
            <TableHeader>
                <tr>
                    <Thead>No.</Thead>
                    <Thead
                        :sortable="true"
                        :sort-info="sortInfo || {}"
                        :sortColumn="'name'"
                    >
                        Name
                    </Thead>
                    <Thead
                        :sortable="true"
                        :sort-info="sortInfo || {}"
                        :sortColumn="'total_amount'"
                    >
                        Amount
                    </Thead>
                    <Thead>Payment Method</Thead>
                    <Thead>Payment Status</Thead>
                    <Thead>Actions</Thead>
                </tr>
            </TableHeader>
            <tbody>
                <TableZebraRows
                    v-for="(payment, index) in payments.data"
                    :key="payment.id"
                >
                    <td class="px-6 py-4">{{ index + 1 + (payments.meta.current_page - 1) * payments.meta.per_page }}</td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        <Link :href="route('payments.show', { payment: payment.id })">
                            {{ payment.name }}
                        </Link>
                    </th>
                    <td class="px-6 py-4">{{ payment.total_amount }}</td>
                    <td class="px-6 py-4">
                        {{
                            payment.payment_method
                               
                        }}
                    </td>
                    <td class="px-6 py-4">
                        {{
                            payment.payment_status
                        }}
                    </td>
                    <td class="px-6 py-4 flex space-x-6">
                        <div v-if="userCan('view-payments')">
                            <Link :href="route('payments.show', { payment: payment.id })" class="text-blue-500 hover:text-blue-700">
                                <EyeIcon class="w-5 h-5" />
                            </Link>
                        </div>
                        <div v-if="userCan('update-payments')">
                            <Link :href="route('payments.edit', { payment: payment.id })" class="text-green-500 hover:text-green-700">
                                <PencilSquareIcon class="w-5 h-5" />
                            </Link>
                        </div>
                        <div v-if="userCan('delete-payments')">
                            <button @click="deletePayment(payment.id)" class="text-red-500 hover:text-red-700">
                                <TrashIcon class="w-5 h-5" />
                            </button>
                        </div>
                    </td>
                </TableZebraRows>
            </tbody>
        </Table>
    </div>

    <!-- Pagination Links -->
    <div v-if="payments && payments.meta && payments.meta.links" class="mt-3 flex justify-center space-x-6">
        <Link
            v-for="link in payments.meta.links"
            :key="link.label"
            :href="link.url ? `${link.url}&search=${search}` : '#'"
            class="p-2 px-4 text-sm font-medium rounded-lg transition-colors"
            :class="{
                'text-gray-700 dark:text-gray-400': true,
                'cursor-not-allowed opacity-50': !link.url,
                '!bg-gray-100 !dark:bg-gray-800': link.active,
            }"
            v-html="link.label"
        />
    </div>
    <div v-else class="mt-4 text-center text-gray-500 dark:text-gray-400">
        No pagination information available.
    </div>
</template>