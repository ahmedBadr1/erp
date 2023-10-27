@props([
    'title' => '',
    'create_link' => '',    
])
 <div class="block p-10 bg-white border border-gray-200 rounded-lg shadow  dark:bg-gray-800 dark:border-gray-700">
    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"> {{ __('names.'.$title)}} </h5>
        <a href="{{ $create_link }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            {{ __('message.create',['model' => __('names.'.$title)])}}
            <svg class="w-3.5 h-3.5 mr-2 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 5.757v8.486M5.757 10h8.486M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
         </a>
         <div class="relative overflow-x-auto mt-4 ">
            <table class="w-full  text-sm   text-gray-500 dark:text-gray-400">
               {{ $slot }}
            </table>
        </div>
</div>