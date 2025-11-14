<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    public function dashboard()
    {
        $investor = auth()->user()->investor;
        
        $stats = [
            'total_wishlists' => $investor->wishlists()->count(),
        ];

        $recentWishlists = $investor->wishlistProjects()
            ->with(['student.user', 'media', 'categories'])
            ->latest('wishlists.created_at')
            ->limit(6)
            ->get();

        return view('investor.dashboard', compact('investor', 'stats', 'recentWishlists'));
    }

    public function edit(Investor $investor)
    {
        $this->authorize('update', $investor);

        return view('investor.edit', compact('investor'));
    }

    public function update(Request $request, Investor $investor)
    {
        $this->authorize('update', $investor);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048',
            'short_about' => 'nullable|string|max:500',
            'about' => 'nullable|string',
        ]);

        // Update user data
        $userData = [
            'full_name' => $validated['full_name'],
            'phone_number' => $validated['phone_number'],
            'short_about' => $validated['short_about'] ?? null,
            'about' => $validated['about'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            if ($investor->user->avatar) {
                Storage::disk('public')->delete($investor->user->avatar);
            }
            $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $investor->user->update($userData);

        // Update investor data
        $investor->update([
            'company_name' => $validated['company_name'],
            'industry' => $validated['industry'],
        ]);

        return redirect()->route('investor.dashboard')
            ->with('success', 'Profile updated successfully!');
    }
}

