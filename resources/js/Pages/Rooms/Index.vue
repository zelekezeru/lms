<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    EyeIcon,
    TrashIcon,
    ArrowPathIcon,
    Squares2X2Icon,
    TableCellsIcon,
    PencilSquareIcon,
    MapPinIcon,
} from "@heroicons/vue/24/outline";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "@/Components/TableZebraRows.vue";
import Thead from "@/Components/Thead.vue";

defineProps({
    rooms: Object,
    user: Object,
    sortInfo: Object,
});

const refreshing = ref(false);
const viewMode = ref("card");
const search = ref(usePage().props.search || "");

const refreshData = () => {
    refreshing.value = true;
    router.visit(route("rooms.index"), {
        only: ["rooms"],
        onFinish: () => (refreshing.value = false),
    });
};

const searchRooms = () => {
    router.get(
        route("rooms.index"),
        { search: search.value },
        { preserveState: true }
    );
};
</script>

<template>
    <AppLayout>
        <div class="my-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $t("rooms.title") }}
            </h1>
        </div>

        <!-- Header Toolbar -->
        <div class="flex items-center justify-between mb-3">
            <div class="relative">
                <input
                    type="text"
                    v-model="search"
                    :placeholder="$t('rooms.search_placeholder')"
                    class="p-2 pl-10 text-gray-900 border rounded-lg dark:text-white dark:bg-gray-700"
                    @input="searchRooms"
                />
            </div>

            <div class="flex space-x-4">
                <Link
                    v-if="userCan('create-rooms')"
                    :href="route('rooms.create')"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-green-600 rounded-md hover:bg-green-700"
                >
                    + {{ $t("rooms.add_room") }}
                </Link>

                <button
                    @click="refreshData"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-blue-800 rounded-md hover:bg-blue-700"
                    :title="$t('rooms.refresh')"
                >
                    <ArrowPathIcon
                        class="w-5 h-5 mr-2"
                        :class="{ 'animate-spin': refreshing }"
                    />
                    {{ $t("rooms.refresh") }}
                </button>
                <button
                    @click="viewMode = viewMode === 'table' ? 'card' : 'table'"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-gray-600 rounded-md hover:bg-gray-700"
                    :title="$t('rooms.toggle_view')"
                >
                    <component
                        :is="
                            viewMode === 'table'
                                ? Squares2X2Icon
                                : TableCellsIcon
                        "
                        class="w-5 h-5"
                    />
                </button>
            </div>
        </div>

        <!-- Table View -->
        <div v-if="viewMode === 'table'">
            <Table>
                <TableHeader>
                    <tr>
                        <Thead
                            :sortable="true"
                            :sort-info="sortInfo"
                            sortColumn="name"
                        >
                            {{ $t("rooms.name") }}
                        </Thead>
                        <Thead
                            :sortable="true"
                            :sort-info="sortInfo"
                            sortColumn="capacity"
                        >
                            {{ $t("rooms.capacity") }}
                        </Thead>
                        <Thead>{{ $t("rooms.location") }}</Thead>
                        <Thead>{{ $t("rooms.action") }}</Thead>
                    </tr>
                </TableHeader>

                <tbody>
                    <TableZebraRows v-for="room in rooms.data" :key="room.id">
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-gray-900 dark:text-white"
                        >
                            <Link
                                :href="route('rooms.show', { room: room.id })"
                                >{{ room.name }}</Link
                            >
                        </th>
                        <td class="px-6 py-4">{{ room.capacity }}</td>
                        <td class="px-6 py-4">{{ room.location }}</td>
                        <td class="flex px-6 py-4 space-x-2">
                            <Link
                                v-if="userCan('view-rooms')"
                                :href="route('rooms.show', { room: room.id })"
                                class="text-blue-500 hover:text-blue-700"
                            >
                                <EyeIcon class="w-5 h-5" />
                            </Link>
                            <Link
                                v-if="userCan('update-rooms')"
                                :href="route('rooms.edit', { room: room.id })"
                                class="text-green-500 hover:text-green-700"
                            >
                                <PencilSquareIcon class="w-5 h-5" />
                            </Link>
                        </td>
                    </TableZebraRows>
                </tbody>
            </Table>
        </div>

        <!-- Card View -->
        <div
            v-else
            class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
        >
            <div
                v-for="room in rooms.data"
                :key="room.id"
                class="p-4 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-600"
            >
                <div
                    class="flex items-center mb-3 font-bold text-gray-700 dark:text-gray-300"
                >
                    <MapPinIcon class="w-5 h-5 mr-2 text-indigo-500" />
                    {{ room.name }}
                </div>
                <div class="mb-2 text-gray-700 dark:text-gray-300">
                    <strong>{{ $t("rooms.capacity") }}:</strong>
                    {{ room.capacity }}
                </div>
                <div class="mb-2 text-gray-700 dark:text-gray-300">
                    <strong>{{ $t("rooms.location") }}:</strong>
                    {{ room.location }}
                </div>
                <div class="flex justify-end mt-4 space-x-3">
                    <Link
                        :href="route('rooms.show', { room: room.id })"
                        class="text-blue-500 hover:text-blue-700"
                    >
                        <EyeIcon class="w-5 h-5" />
                    </Link>
                    <Link
                        v-if="userCan('update-rooms')"
                        :href="route('rooms.edit', { room: room.id })"
                        class="text-green-500 hover:text-green-700"
                    >
                        <PencilSquareIcon class="w-5 h-5" />
                    </Link>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-6 space-x-2">
            <Link
                v-for="link in rooms.meta.links"
                :key="link.label"
                :href="link.url || '#'"
                class="p-2 px-4 text-sm font-medium transition-colors rounded-lg"
                :class="{
                    'text-gray-700 dark:text-gray-400': true,
                    'cursor-not-allowed opacity-50': !link.url,
                    '!bg-gray-100 !dark:bg-gray-800': link.active,
                }"
                v-html="link.label"
            />
        </div>
    </AppLayout>
</template>
