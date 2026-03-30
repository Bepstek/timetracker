<?php

namespace App\Http\Controllers;

use App\Models\TimeLog;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with Company Batch Cards
     */
    public function dashboard(Request $request): View
    {
        $status = $request->get('status', 'active');
        $companies = Company::where('is_archived', $status === 'archived')
            ->withCount('users')
            ->get();
        
        return view('admin.dashboard', compact('companies', 'status'));
    }

    /**
     * Store a new company
     */
    public function storeCompany(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'batch' => 'required|string|max:100',
        ]);

        Company::create($request->only('name', 'batch', 'address', 'contact_person'));

        return back()->with('success', 'OJT Room created successfully.');
    }

    /**
     * Archive or Restore a company
     */
    public function toggleArchive(Company $company)
    {
        $company->update([
            'is_archived' => !$company->is_archived
        ]);

        if ($company->is_archived) {
            // When archiving, remove this company from all assigned users
            User::where('company_id', $company->id)->update(['company_id' => null]);
        }

        $message = $company->is_archived ? 'OJT Room archived successfully.' : 'OJT Room restored successfully.';
        return back()->with('success', $message);
    }

    /**
     * Show students in a specific company
     */
    public function showCompany(Company $company)
    {
        $company->load(['users' => function($query) use ($company) {
            $query->withSum(['timeLogs' => function($q) use ($company) {
                $q->where('status', 'completed')
                  ->where('company_id', $company->id);
            }], 'total_hours')->with(['timeLogs' => function($q) use ($company) {
                $q->where('company_id', $company->id);
            }]);
        }]);

        return view('admin.company_show', compact('company'));
    }

    /**
     * Assign student to company
     */
    public function assignStudent(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->update(['company_id' => $request->company_id]);

        return back()->with('success', 'Student assigned successfully.');
    }

    /**
     * Remove student from company
     */
    public function removeStudent(User $user)
    {
        $user->update(['company_id' => null]);

        return back()->with('success', 'Student removed from OJT Room.');
    }

    /**
     * Display all users for management
     */
    public function users(Request $request): View
    {
        $query = User::with('company')->orderBy('name');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * Delete a user
     */
    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['message' => 'You cannot delete yourself.']);
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    /**
     * Reset a user's password to a random string
     */
    public function resetPassword(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['message' => 'You cannot reset your own password here. Use settings.']);
        }

        $newPassword = \Illuminate\Support\Str::random(8);
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($newPassword)
        ]);

        return back()->with('reset_password_data', [
            'name' => $user->name,
            'password' => $newPassword
        ]);
    }

    /**
     * Display all time logs with filtering
     */
    public function logs(Request $request): View
    {
        $query = TimeLog::with(['user.company'])->orderBy('date', 'desc')->orderBy('time_in', 'desc');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->paginate(20);
        $users = User::where('role', 'student')->get();

        return view('admin.logs', [
            'logs' => $logs,
            'users' => $users,
            'filters' => [
                'user_id' => $request->user_id,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'status' => $request->status,
            ],
        ]);
    }
}

