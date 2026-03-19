{{-- Single Main Container --}}
<div id="report-printable-area" 
     class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 
            max-h-[75vh] overflow-y-auto relative">
    
    {{-- Report Header --}}
    <div class="mb-8 border-b border-gray-100 pb-4 dark:border-gray-800 text-center">
        <h1 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white uppercase">
            System Development Report
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Generated on {{ now()->format('F d, Y') }}
        </p>
    </div>

    {{-- Report Content (The scrollable part) --}}
    <div class="space-y-8">
        @forelse($records->groupBy('system.name') as $systemName => $updates)
            <div class="print-entry">
                <h2 class="flex items-center gap-2 text-lg font-bold text-primary-600 dark:text-primary-400 border-b border-gray-100 dark:border-gray-800 pb-1 mb-4">
                    <x-heroicon-m-computer-desktop class="w-5 h-5"/>
                    {{ $systemName }}
                </h2>
                
                <ul class="mt-3 space-y-6 border-l-2 border-gray-100 ml-2 pl-6 dark:border-gray-800">
                    @foreach($updates as $update)
                        <li class="relative">
                            {{-- The Bullet Dot --}}
                            <div class="absolute -left-[31px] top-2 h-2.5 w-2.5 rounded-full bg-primary-500 print:bg-gray-300"></div>
                            
                            <div class="flex flex-col">
                                {{-- Update Title --}}
                                <span class="font-bold text-gray-900 dark:text-gray-100 text-md">
                                    {{ $update->title }}
                                </span>

                                {{-- Update Description (New Section) --}}
                                @if($update->description)
                                    <div class="mt-1 text-sm text-gray-700 dark:text-gray-300 leading-relaxed italic print:not-italic">
                                        {!! $update->description !!}
                                    </div>
                                @endif

                                {{-- Metadata --}}
                                <span class="mt-2 text-[10px] text-gray-500 uppercase tracking-widest font-medium">
                                    {{ $update->created_at->format('M d, Y') }} — Dev: {{ $update->user->name }}
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @empty
            <p class="text-center text-gray-500 italic">No updates found for the selected filters.</p>
        @endforelse
    </div>

    {{-- Sticky Footer - Stays at the bottom of the modal while you scroll --}}
    <div class="mt-10 flex justify-end gap-3 border-t pt-6 dark:border-gray-800 no-print 
                sticky bottom-[-24px] bg-white dark:bg-gray-900 pb-4 z-10">
        <x-filament::button color="gray" @click="close">
            Close
        </x-filament::button>

        <x-filament::button icon="heroicon-m-printer" onclick="window.print()">
            Print Report
        </x-filament::button>
    </div>
</div>
<style>
    @media print {
        /* 1. Kill the Modal "Shadow" and "Dark Background" */
        .fi-modal-window, 
        .fi-section,
        #report-printable-area {
            background-color: white !important;
            background: white !important;
            box-shadow: none !important;
            ring: none !important;
            border: none !important;
            outline: none !important;
            color: black !important;
        }

        /* 2. Nuclear Reset for Dark Mode */
        html, body, .fi-main, .fi-content, .fi-modal-container {
            background-color: white !important;
            background: white !important;
            color: black !important;
        }

        /* 3. Force Text Visibility */
        #report-printable-area h1, 
        #report-printable-area h2, 
        #report-printable-area span, 
        #report-printable-area li,
        #report-printable-area div {
            color: black !important;
            background-color: transparent !important;
        }

        /* 4. Hide UI Elements */
        .no-print, 
        .fi-modal-close-button, 
        .fi-topbar, 
        .fi-sidebar,
        .fi-modal-header {
            display: none !important;
        }

        /* 5. Force the Print Area to fill the page */
        #report-printable-area {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            visibility: visible !important;
            z-index: 99999 !important;
        }

        /* 6. Fix for those absolute dots turning into black squares */
        .relative div[class*="rounded-full"] {
            border: 1px solid black !important;
            background-color: white !important;
        }
    }
</style>