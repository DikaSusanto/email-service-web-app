<template>
    <form class="flex items-center">
        <label for="simple-search" class="sr-only">Search</label>
        <div class="relative w-full">
            <div
                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
            >
                <svg
                    aria-hidden="true"
                    class="w-5 h-5 text-gray-500"
                    fill="currentColor"
                    viewbox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        fill-rule="evenodd"
                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                        clip-rule="evenodd"
                    />
                </svg>
            </div>
            <input
                type="text"
                id="search"
                class="block w-full py-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400"
                placeholder="Cari..."
                v-model="searchQuery"
                @input="handleInput"
            />
        </div>
    </form>
</template>

<script setup>
import { ref, watch } from "vue";
import { debounce } from "lodash";

const searchQuery = ref();

const emit = defineEmits(["search"]);

const emitSearch = debounce((query) => {
    emit("search", query);
}, 300);

const handleInput = () => {
    emitSearch(searchQuery.value);
};
</script>
