<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\SavedAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavedAddressController extends Controller
{
    public function index()
    {
        $addresses = SavedAddress::where('user_id', Auth::id())
            ->orderByDesc('is_default')
            ->orderBy('label')
            ->get();

        return view('shop.addresses.index', compact('addresses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label'        => ['required', 'string', 'max:100'],
            'contact_name' => ['nullable', 'string', 'max:150'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'address'      => ['required', 'string', 'max:255'],
            'city'         => ['required', 'string', 'max:100'],
            'postal_code'  => ['required', 'string', 'max:10'],
            'country'      => ['required', 'string', 'size:2'],
            'is_default'   => ['boolean'],
        ]);

        $data['user_id'] = Auth::id();

        if (! empty($data['is_default'])) {
            SavedAddress::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        SavedAddress::create($data);

        return back()->with('success', __('app.address_saved'));
    }

    public function update(Request $request, int $id)
    {
        $address = SavedAddress::where('user_id', Auth::id())->findOrFail($id);

        $data = $request->validate([
            'label'        => ['required', 'string', 'max:100'],
            'contact_name' => ['nullable', 'string', 'max:150'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'address'      => ['required', 'string', 'max:255'],
            'city'         => ['required', 'string', 'max:100'],
            'postal_code'  => ['required', 'string', 'max:10'],
            'country'      => ['required', 'string', 'size:2'],
        ]);

        $address->update($data);

        return back()->with('success', __('app.address_updated'));
    }

    public function setDefault(int $id)
    {
        SavedAddress::where('user_id', Auth::id())->update(['is_default' => false]);
        SavedAddress::where('user_id', Auth::id())->findOrFail($id)->update(['is_default' => true]);

        return back()->with('success', __('app.address_set_default'));
    }

    public function destroy(int $id)
    {
        SavedAddress::where('user_id', Auth::id())->findOrFail($id)->delete();

        return back()->with('success', __('app.address_deleted'));
    }
}
