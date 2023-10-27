<div>
    <x-container title="roles" :create_link="route('admin.roles.create')">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            {{ __('names.role-name')}}
                        </th>
                        <th scope="col" class="px-6 py-3">
                            {{ __('names.permissions')}}
                        </th>
                       
                        <th scope="col" class="px-6 py-3">
                            -
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-center bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Apple MacBook Pro 17"
                        </th>
                        <td scope="col" class="px-6 py-3">
                            Silver
                        </td>
                        
                        <td scope="col" class="px-6 py-3">
                            <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                {{ __('Edit')}}
                            </a>
                            <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                {{ __('Delete')}}
                            </a>
                        </td>
                    </tr>
                   
                </tbody>
    </x-container>
</div>
