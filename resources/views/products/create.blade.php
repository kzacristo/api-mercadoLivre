<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required autofocus />

            <x-input-label for="description" :value="__('Description')" />
            <x-text-input id="description" class="block mt-1 w-full" type="text" name="description" required />

            <x-input-label for="price" :value="__('Price')" />
            <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" min="0" name="price" required />

            <x-input-label for="stock" :value="__('Stock')" />
            <x-text-input id="stock" class="block mt-1 w-full" type="number" min="0" name="stock" required />

            <x-input-label for="category_id" :value="__('Category ID')" />
            <x-text-input id="category_id" class="block mt-1 w-full" type="number" min="0" name="category_id" required />

            <x-input-label for="image" :value="__('Image')" />
            <x-text-input id="image" class="block mt-1 w-full" type="file" name="image" required />
        </div>

        <div class="flex justify-center mt-4">
            <x-primary-button>
                {{ __('Submit') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
