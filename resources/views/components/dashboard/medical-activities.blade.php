<div class="bg-white p-6 rounded-lg shadow-sm">
    <h3 class="text-lg font-semibold mb-4">Recent DCR Activity</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DCR ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- Placeholder for activity log -->
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">DCR-001</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Submitted for approval</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Admin User</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ now()->toFormattedDateString() }}</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">DCR-000</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Closed</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Admin User</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ now()->subDays(5)->toFormattedDateString() }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
