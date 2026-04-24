<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Storage;
use App\Services\AuditLogger;

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

        $menuItem = MenuItem::create($validated);

        AuditLogger::log('admin.menu_item.created', [
            'name' => $menuItem->name,
            'category' => $menuItem->category,
            'price' => $menuItem->price,
            'stock' => $menuItem->stock,
            'has_image' => (bool) $menuItem->image,
        ], $request, MenuItem::class, $menuItem->id);

        return redirect()->route('admin.menu')
            ->with('success', 'Menu item created successfully');
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $before = $menuItem->only(['name', 'category', 'price', 'stock', 'image']);

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

        $after = $menuItem->fresh()->only(['name', 'category', 'price', 'stock', 'image']);
        AuditLogger::log('admin.menu_item.updated', [
            'before' => $before,
            'after' => $after,
        ], $request, MenuItem::class, $menuItem->id);

        return redirect()->route('admin.menu')
            ->with('success', 'Menu item updated successfully');
    }

    public function destroy(MenuItem $menuItem)
    {
        $snapshot = $menuItem->only(['id', 'name', 'category', 'price', 'stock', 'image']);

        if ($menuItem->image && Storage::disk('public')->exists($menuItem->image)) {
            Storage::disk('public')->delete($menuItem->image);
        }

        $menuItem->delete();

        AuditLogger::log('admin.menu_item.deleted', [
            'menu_item' => $snapshot,
        ], request(), MenuItem::class, $snapshot['id'] ?? null);

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