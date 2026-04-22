<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::all();
        return view('Admin.menu.index', compact('menuItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        MenuItem::create($validated);

        return redirect()->route('admin.menu')
            ->with('success', 'Menu item created successfully');
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // ✅ handle image update properly
        if ($request->hasFile('image')) {

            // delete old image
            if ($menuItem->image && Storage::disk('public')->exists($menuItem->image)) {
                Storage::disk('public')->delete($menuItem->image);
            }

            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        } else {
            // keep old image if no new upload
            $validated['image'] = $menuItem->image;
        }

        $menuItem->update($validated);

        return redirect()->route('admin.menu')
            ->with('success', 'Menu item updated successfully');
    }

    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->image && Storage::disk('public')->exists($menuItem->image)) {
            Storage::disk('public')->delete($menuItem->image);
        }

        $menuItem->delete();

        return redirect()->route('admin.menu')
            ->with('success', 'Menu item deleted successfully');
    }

    public function edit(MenuItem $menuItem)
{
    return response()->json([
        'id' => $menuItem->id,
        'name' => $menuItem->name,
        'category' => $menuItem->category,
        'price' => $menuItem->price,
        'stock' => $menuItem->stock,
        'image' => $menuItem->image,
        'image_url' => $menuItem->image ? asset('storage/' . $menuItem->image) : null,
    ]);
  }
}