<div class="flex justify-center mt-4 space-x-2">
    <button @click="prevPage()" :disabled="currentPage === 1"
        class="px-3 py-1 rounded bg-yellow-200 hover:bg-yellow-300 disabled:opacity-50">Prev</button>

    <template x-for="page in visiblePages()" :key="page">
        <button @click="goToPage(page)"
            :class="{'bg-yellow-500 text-white': page === currentPage, 'bg-yellow-200': page !== currentPage}"
            class="px-3 py-1 rounded hover:bg-yellow-300">
            <span x-text="page"></span>
        </button>
    </template>

    <button @click="nextPage()" :disabled="currentPage === totalPages()"
        class="px-3 py-1 rounded bg-yellow-200 hover:bg-yellow-300 disabled:opacity-50">Next</button>
</div>
