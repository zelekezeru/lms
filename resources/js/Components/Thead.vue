<script setup>
import { ArrowDownIcon, ArrowUpIcon } from "@heroicons/vue/24/outline";
import { ChevronUpDownIcon } from "@heroicons/vue/24/solid";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    sortable: {
        type: Boolean,
        required: false,
        default: false,
    },
    sortColumn: {
        type: String,
        required: false,
    },
    sortInfo: {
        type: Object,
    },
    reloadOnly: {
        type: String,
    },
});

// This variable stores which props to reload after sorting in addition to the
const reloadOnly = route().current().split(".")[0];
const sort = () => {
    let sortDirection = "desc";

    // If there's no current sort info, default to "desc"
    if (
        !props.sortInfo.currentSortColumn ||
        !props.sortInfo.currentSortDirection
    ) {
        sortDirection = "desc";
    } else if (
        props.sortInfo.currentSortColumn === props.sortColumn &&
        props.sortInfo.currentSortDirection === "asc"
    ) {
        // If we're already sorting this column in ascending order, toggle to "desc"
        sortDirection = "desc";
    } else {
        // Otherwise, set to "asc"
        sortDirection = "asc";
    }

    const currentParams = new URLSearchParams(window.location.search);

    const queries = Object.fromEntries(currentParams.entries());

    router.get(
        route(route().current()),
        {
            ...queries,
            sortColumn: props.sortColumn,
            sortDirection: sortDirection,
        },
        {
            preserveState: true,
            only: [reloadOnly, "sortInfo"],
        }
    );
};
</script>

<template>
    <th scope="col" class="px-6 py-3">
        <div
            class="flex gap-3 items-center text-gray-700 dark:text-gray-200"
            :class="{
                'hover:text-red-500 dark:hover:text-gray-100 cursor-pointer':
                    sortable,
                '!text-yellow-400':
                    sortable && sortInfo.currentSortColumn == sortColumn,
            }"
            :title="`Click to sort by ${sortColumn}`"
            @click="sort"
        >
            <slot />
            <template v-if="sortable">
                <ArrowUpIcon
                    class="w-4"
                    v-if="
                        sortInfo.currentSortColumn == sortColumn &&
                        sortInfo.currentSortDirection == 'desc'
                    "
                />
                <ArrowDownIcon class="w-4" v-else />
            </template>
        </div>
    </th>
</template>
