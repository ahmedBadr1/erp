<div dir="ltr"
     x-data="noticesHandler()"
     class="fixed inset-0 flex flex-col items-end justify-start h-screen w-screen z-[1500] "
     @notice.window="add($event.detail)"
     style="pointer-events:none">
    <template x-for="notice of notices" :key="notice.id">
        <div
            x-show="visible.includes(notice)"
            x-transition:enter="transition ease-in duration-200"
            x-transition:enter-start="transform opacity-0 translate-y-2"
            x-transition:enter-end="transform opacity-100"
            x-transition:leave="transition ease-out duration-500"
            x-transition:leave-start="transform translate-x-0 opacity-100"
            x-transition:leave-end="transform translate-x-full opacity-0"
            @click="remove(notice.id)"
            class="inline-flex items-center justify-center flex-shrink-0 w-1/6 h-1/8 mt-3 max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800"
            style="pointer-events:all">
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ">
                <i class="bx  bx-sm p-1 rounded-lg "
                   :class="{
				'bx-check-circle text-emerald-500 bg-emerald-100 dark:bg-emerald-800 dark:text-emerald-200': notice.type === 'success',
				'bx-info-circle text-indigo-500 bg-indigo-100 dark:bg-indigo-800 dark:text-indigo-200': notice.type === 'info',
				'bx-error-circle text-orange-500 bg-orange-100 dark:bg-orange-800 dark:text-orange-200': notice.type === 'warning',
				'bx-x-circle text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200': notice.type === 'error',
			 }"

                ></i>
                <span class="sr-only">Check icon</span>
            </div>

        <div class="ml-3 text-sm font-normal"   x-text="notice.text"></div>
            <button type="button"    @click="remove(notice.id)" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" data-dismiss-target="#toast-success" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>

    </template>
</div>

