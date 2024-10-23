<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;

class ProductController extends Controller
{
    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required',
            'image' => 'nullable|image'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads', 'public');
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'image' => $imagePath,
        ]);


        $accessToken = env('MERCADO_LIVRE_ACCESS_TOKEN');

        if (!$accessToken) {
            return redirect()->route('products.index')->with('success', 'Product created successfully.');
        }

        $client = new Client();
        $response = $client->post('https://api.mercadolibre.com/items', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'title' => $product->name,
                'price' => $product->price,
                'category_id' => $product->category_id,
                'available_quantity' => $product->stock,
                'pictures' => [
                    ['source' => asset('storage/' . $product->image)],
                ],
            ],
        ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully.')->json(json_decode($response->getBody()->getContents()));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required',
            'image' => 'nullable|image'
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads', 'public');
            $product->image = $imagePath;
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
        ]);

        $accessToken = env('MERCADO_LIVRE_ACCESS_TOKEN');

        if (!$accessToken) {
            return redirect()->route('products.index')->with('error', 'Access token not available.');
        }

        $client = new Client();

        $response = $client->get('https://api.mercadolibre.com/items/' . $product->ml_product_id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        if ($response->getStatusCode() === 404) {
            $createResponse = $client->post('https://api.mercadolibre.com/items', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'title' => $product->name,
                    'price' => $product->price,
                    'category_id' => $product->category_id,
                    'available_quantity' => $product->stock,
                    'pictures' => [
                        ['source' => asset('storage/' . $product->image)],
                    ],
                ],
            ]);


            $mlResponse = json_decode($createResponse->getBody()->getContents(), true);
            $product->ml_product_id = $mlResponse['id'];
            $product->save();

            return redirect()->route('products.index')->with('success', 'Product created successfully on Mercado Livre.');
        } else {

            $client->put('https://api.mercadolibre.com/items/' . $product->ml_product_id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'title' => $product->name,
                    'price' => $product->price,
                    'category_id' => $product->category_id,
                    'available_quantity' => $product->stock,
                    'pictures' => [
                        ['source' => asset('storage/' . $product->image)],
                    ],
                ],
            ]);

            return redirect()->route('products.index')->with('success', 'Product updated successfully on Mercado Livre.');
        }
    }



    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }


    public function handleCallback(Request $request)
    {
        $client = new Client();
        $response = $client->post('https://api.mercadolibre.com/oauth/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => env('MERCADO_LIVRE_CLIENT_ID'),
                'client_secret' => env('MERCADO_LIVRE_CLIENT_SECRET'),
                'code' => $request->code,
                'redirect_uri' => env('MERCADO_LIVRE_REDIRECT_URI'),
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        file_put_contents(base_path('.env'), "\nMERCADO_LIVRE_ACCESS_TOKEN=" . $data['access_token'], FILE_APPEND);

        return redirect()->route('products.create')->with('success', 'Product created successfully.')->json(json_decode($response->getBody()->getContents()));
    }

    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function republish(Product $product)
    {
        $accessToken = env('MERCADO_LIVRE_ACCESS_TOKEN');

        if (!$accessToken) {
            return redirect()->route('products.index')->with('error', 'Access token not found.');
        }

        $client = new Client();
        $response = $client->post('https://api.mercadolibre.com/items', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'title' => $product->name,
                'price' => $product->price,
                'category_id' => $product->category_id,
                'available_quantity' => $product->stock,
                'pictures' => [
                    ['source' => asset('storage/' . $product->image)],
                ],
            ],
        ]);

        return redirect()->route('products.index')->with('success', 'Product republished successfully.')->json(json_decode($response->getBody()->getContents()));
    }
}
