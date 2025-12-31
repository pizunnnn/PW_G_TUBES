<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display list of users
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');
        
        $users = User::query()
            ->when($search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%")
                           ->orWhere('phone', 'like', "%{$search}%");
            })
            ->when($role, function($query, $role) {
                return $query->where('role', $role);
            })
            ->withCount('transactions')
            ->latest()
            ->paginate(15);
        
        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    /**
     * Show user detail with transactions
     */
    public function show(User $user)
    {
        $user->load(['transactions.product']);
        
        $stats = [
            'total_transactions' => $user->transactions->count(),
            'total_spent' => $user->transactions->where('payment_status', 'paid')->sum('total_price'),
            'pending_transactions' => $user->transactions->where('payment_status', 'pending')->count(),
            'completed_transactions' => $user->transactions->where('payment_status', 'paid')->count(),
        ];
        
        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Delete user (optional - be careful!)
     */
    public function destroy(User $user)
    {
        // Prevent deleting admin
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot delete admin user!');
        }

        // Prevent deleting user with transactions
        if ($user->transactions()->count() > 0) {
            return back()->with('error', 'Cannot delete user with existing transactions!');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Toggle user role (optional)
     */
    public function toggleRole(User $user)
    {
        // Prevent changing own role
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot change your own role!');
        }

        $newRole = $user->role === 'admin' ? 'user' : 'admin';
        $user->update(['role' => $newRole]);

        return back()->with('success', 'User role updated to ' . $newRole . '!');
    }
    
}