<div x-data="toast()" 
     x-init="init()"
     @notify.window="show($event.detail.message, $event.detail.type)"
     x-show="visible"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-full"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform translate-y-full"
     class="fixed bottom-4 right-4 z-50 max-w-sm"
     style="display: none;">
    
    <div class="rounded-lg shadow-lg p-4 mb-2"
         :class="{
             'bg-green-500 text-white': type === 'success',
             'bg-red-500 text-white': type === 'error',
             'bg-yellow-500 text-white': type === 'warning',
             'bg-blue-500 text-white': type === 'info'
         }">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg x-show="type === 'success'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg x-show="type === 'error'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <svg x-show="type === 'warning'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <svg x-show="type === 'info'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium" x-text="message"></p>
            </div>
            <div class="ml-auto pl-3">
                <button @click="hide()" class="inline-flex text-white hover:text-gray-200 focus:outline-none">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function toast() {
    return {
        visible: false,
        message: '',
        type: 'info',
        timeout: null,
        
        init() {
            // Check for session messages on page load
            @if(session('success'))
                this.show('{{ session('success') }}', 'success');
            @endif
            @if(session('error'))
                this.show('{{ session('error') }}', 'error');
            @endif
            @if(session('warning'))
                this.show('{{ session('warning') }}', 'warning');
            @endif
            @if(session('info'))
                this.show('{{ session('info') }}', 'info');
            @endif
        },
        
        show(message, type = 'info') {
            this.message = message;
            this.type = type;
            this.visible = true;
            
            // Auto hide after 5 seconds
            if (this.timeout) {
                clearTimeout(this.timeout);
            }
            
            this.timeout = setTimeout(() => {
                this.hide();
            }, 5000);
        },
        
        hide() {
            this.visible = false;
            if (this.timeout) {
                clearTimeout(this.timeout);
            }
        }
    }
}
</script>
