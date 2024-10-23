<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Description</th>
                                <th class="px-4 py-2">Price</th>
                                <th class="px-4 py-2">Stock</th>
                                <th class="px-4 py-2">Exists in Mercado Livre</th>
                                <th class="px-4 py-2">Actions</th>
                                <th class="px-4 py-2">Re-publish</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                            <tr>
                                <td class="border px-4 py-2">{{ $product->name }}</td>
                                <td class="border px-4 py-2">{{ $product->description }}</td>
                                <td class="border px-4 py-2">{{ $product->price }}</td>
                                <td class="border px-4 py-2">{{ $product->stock }}</td>
                                <td class="border px-4 py-2">
                                    @if ($product->ml_product_id)
                                    <span class="text-green-500">Yes</span>
                                    @else
                                    <span class="text-red-500">No</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2">
                                    <a href="{{ route('products.edit', $product->id) }}" class="text-blue-500 hover:underline">Edit</a>
                                </td>
                                <td class="border px-4 py-2">
                                    @if (!$product->ml_product_id)
                                    <form action="{{ route('products.republish', $product->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-yellow-500 hover:underline">Re-publish</button>
                                    </form>
                                    @else
                                    <span class="text-gray-500">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>