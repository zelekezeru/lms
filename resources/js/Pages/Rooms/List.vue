<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";
import { EyeIcon, TrashIcon } from "@heroicons/vue/24/solid";
import { PencilSquareIcon } from "@heroicons/vue/24/outline";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "@/Components/TableZebraRows.vue";
import Thead from "@/Components/Thead.vue";

defineProps({
    rooms: {
        type: Object,
        required: true,
    },
    sortInfo: {
        type: Object,
        required: false,
    },
    deleteRoom: {
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
                    <th scope="col" class="px-6 py-3">No.</th>
                    <th scope="col" class="px-6 py-3">Name</th>
                    <th scope="col" class="px-6 py-3">Capacity</th>
                    <th scope="col" class="px-6 py-3">Location</th>
                    <th scope="col" class="px-6 py-3">Actions</th>
                </tr>
            </TableHeader>
            <tbody>
                <TableZebraRows
                    v-for="(room, index) in rooms.data"
                    :key="room.id"
                >
                    <td class="px-6 py-4">
                        {{
                            index +
                            1 +
                            (rooms.meta.current_page - 1) * rooms.meta.per_page
                        }}
                    </td>
                    <th
                        scope="row"
                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                    >
                        <Link :href="route('rooms.show', { room: room.id })">
                            {{ room.name }}
                        </Link>
                    </th>

                    <td class="px-6 py-4">{{ room.capacity }}</td>
                    <td class="px-6 py-4">{{ room.location }}</td>
                    <td class="px-6 py-4 flex space-x-6">
                        <!-- View -->
                        <div v-if="userCan('view-rooms')">
                            <Link
                                :href="route('rooms.show', { room: room.id })"
                                class="text-blue-500 hover:text-blue-700"
                            >
                                <EyeIcon class="w-5 h-5" />
                            </Link>
                        </div>
                        <div v-if="userCan('update-rooms')">
                            <Link
                                :href="route('rooms.edit', { room: room.id })"
                                class="text-green-500 hover:text-green-700"
                            >
                                <PencilSquareIcon class="w-5 h-5" />
                            </Link>
                        </div>
                    </td>
                </TableZebraRows>
            </tbody>
        </Table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-3 flex justify-center space-x-6">
        <Link
            v-for="link in rooms.meta.links"
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
</template>
