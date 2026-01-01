<div class="bg-white p-6 rounded-lg shadow-sm">
    <div class="flex items-center">
        <img class="h-16 w-16 rounded-full" src="https://i.pravatar.cc/150?u={{ auth()->user()->id }}" alt="User avatar">
        <div class="ml-4">
            <h3 class="text-lg font-semibold">{{ auth()->user()->name }}</h3>
            <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
        </div>
        <div class="ml-auto grid grid-cols-2 gap-x-8 gap-y-4 text-sm">
            <div><strong>Submitted DCRs</strong> {{-- SUBMITTED_COUNT --}}</div>
            <div><strong>Approved DCRs</strong> {{-- APPROVED_COUNT --}}</div>
            <div><strong>Pending DCRs</strong> {{-- PENDING_COUNT --}}</div>
            <div><strong>Rejected DCRs</strong> {{-- REJECTED_COUNT --}}</div>
        </div>
    </div>
</div>
