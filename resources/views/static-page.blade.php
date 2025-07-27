<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $page->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($page->meta_description)
                        <div class="text-gray-600 text-lg mb-6">
                            {{ $page->meta_description }}
                        </div>
                    @endif
                    
                    <div class="prose max-w-none">
                        {!! $page->content !!}
                    </div>
                    
                    <div class="mt-8 text-sm text-gray-500 border-t pt-4">
                        Last updated: {{ $page->updated_at->format('F j, Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
