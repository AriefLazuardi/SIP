@props(['message'])

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-90"
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-90"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white">
                <div class="items center">
                    <span class="material-icons text-9xl text-primaryColor px-44 text-center">
                        check_circle_outline
                    </span>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4">
                        <h3 class="text-lg font-medium leading-6 text-customColor" id="modal-title">
                            {{ $message }}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="m-1">
                <button 
                    type="button" 
                    @click="show = false" 
                    class="inline-flex items-center py-3 w-full px-60 bg-primaryColor border-2 border-transparent rounded-md text-sm font-semibold text-white tracking-wide shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-green-400 focus:ring-opacity-50 transition ease-in-out duration-200">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>
