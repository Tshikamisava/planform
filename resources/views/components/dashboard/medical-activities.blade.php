<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <div class="px-3 py-4 sm:px-4 sm:py-5 lg:px-6">
        <h3 class="text-base sm:text-lg leading-6 font-medium text-gray-900">Recent DCR Activity</h3>
    </div>
    <div class="overflow-x-auto -mx-3 sm:mx-0">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-blue-800">
                <tr>
                    <th scope="col" class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Date</th>
                    <th scope="col" class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Request or / Author</th>
                    <th scope="col" class="hidden md:table-cell px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Entry by / Recipient</th>
                    <th scope="col" class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Request</th>
                    <th scope="col" class="hidden lg:table-cell px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Reason</th>
                    <th scope="col" class="hidden lg:table-cell px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Description*</th>
                    <th scope="col" class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Change Impact Rating</th>
                    <th scope="col" class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- Placeholder for activity log -->
                <tr>
                    <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                        {{ now()->format('M d, Y') }}
                    </td>
                    <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-900">
                        DCR-001<br>
                        <span class="text-gray-500">Admin User</span>
                    </td>
                    <td class="hidden md:table-cell px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-900">
                        System Admin<br>
                        <span class="text-gray-500">Not Assigned</span>
                    </td>
                    <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-900">
                        <span class="text-blue-600">Submitted for approval</span>
                    </td>
                    <td class="hidden lg:table-cell px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-900">
                        System update required
                    </td>
                    <td class="hidden lg:table-cell px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-900">
                        Update system configuration for improved performance
                    </td>
                    <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Medium
                        </span>
                    </td>
                    <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap text-right text-xs sm:text-sm font-medium">
                        <span class="text-blue-600 hover:text-blue-900">View</span>
                    </td>
                </tr>
                <tr>
                    <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                        {{ now()->subDays(5)->format('M d, Y') }}
                    </td>
                    <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-900">
                        DCR-000<br>
                        <span class="text-gray-500">Admin User</span>
                    </td>
                    <td class="hidden md:table-cell px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-900">
                        System Admin<br>
                        <span class="text-gray-500">John Smith</span>
                    </td>
                    <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-900">
                        <span class="text-blue-600">Closed</span>
                    </td>
                    <td class="hidden lg:table-cell px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-900">
                        Maintenance completed
                    </td>
                    <td class="hidden lg:table-cell px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-900">
                        Routine system maintenance and updates applied successfully
                    </td>
                    <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Low
                        </span>
                    </td>
                    <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap text-right text-xs sm:text-sm font-medium">
                        <span class="text-blue-600 hover:text-blue-900">View</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
