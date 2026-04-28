@extends('admin.layout')

@section('content')
<div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin.dashboard') }}" class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] hover:text-black transition-colors flex items-center group">
                <svg class="h-4 w-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to OJT Rooms
            </a>
        </div>
        <div class="flex items-center gap-4 mb-4">
            <div class="bg-black text-white px-3 py-1 text-[10px] font-black uppercase tracking-tighter">
                {{ $company->batch }}
            </div>
            @if($company->is_archived)
                <div class="bg-red-600 text-white px-3 py-1 text-[10px] font-black uppercase tracking-tighter">
                    Archived
                </div>
            @endif
        </div>
        <h1 class="text-5xl font-black uppercase tracking-tight text-black leading-none mb-3">{{ $company->name }}</h1>
        <p class="text-gray-500 font-bold uppercase text-[11px] tracking-widest flex items-center">
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            {{ $company->address ?: 'OFF-SITE' }}
        </p>
    </div>
    
    <div class="flex gap-4">
        @if(!$company->is_archived)
            <button onclick="document.getElementById('assignStudentModal').classList.remove('hidden')" class="bg-black text-white px-8 py-4 text-xs font-black uppercase tracking-widest hover:bg-gray-800 transition-all">
                Assign Student
            </button>
        @endif
        <a href="{{ route('admin.companies.exportAnalytics', $company) }}" class="bg-black text-white px-8 py-4 text-xs font-black uppercase tracking-widest hover:bg-gray-800 transition-all inline-flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Export Analytics
        </a>
        <form action="{{ route('admin.companies.archive', $company) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="border-2 border-black px-8 py-4 text-xs font-black uppercase tracking-widest hover:bg-black hover:text-white transition-all">
                {{ $company->is_archived ? 'Restore Room' : 'Archive Room' }}
            </button>
        </form>
    </div>
</div>

<div class="bw-panel overflow-hidden border-2 border-black">
    <div class="px-8 py-6 border-b-2 border-black flex justify-between items-center bg-gray-50">
        <h3 class="text-xs font-black text-black uppercase tracking-[0.2em]">Enrolled Students</h3>
        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $company->users->count() }} Total</span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="bg-white border-b border-gray-100">
                    <th class="px-8 py-5 text-[10px] font-black text-black uppercase tracking-widest">Student</th>
                    <th class="px-8 py-5 text-[10px] font-black text-black uppercase tracking-widest">Performance</th>
                    <th class="px-8 py-5 text-[10px] font-black text-black uppercase tracking-widest text-right">Reference</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($company->users as $student)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-8 py-6">
                        <div class="flex items-center">
                            <div class="h-10 w-10 bg-black flex items-center justify-center text-white font-black text-xs uppercase mr-4">
                                {{ substr($student->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-black text-black uppercase">{{ $student->name }}</p>
                                <p class="text-[10px] font-bold text-gray-400">{{ $student->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex items-center max-w-xs">
                            @php
                                // Use the rendered_hours from the controller if available, otherwise calculate it.
                                $renderedHours = $student->rendered_hours ?? 0;
                                $hours = floor($renderedHours);
                                $minutes = round(($renderedHours - $hours) * 60);
                            @endphp
                            <span class="text-sm font-black text-black mr-4 tabular-nums">{{ $hours }}h {{ $minutes }}m</span>
                            <div class="flex-1 h-2 bg-gray-100 overflow-hidden">
                                @php 
                                    $progress = min(100, ($renderedHours / 486) * 100);
                                @endphp
                                <div class="bg-black h-full transition-all duration-[1.5s]" style="width: {{ $progress }}%"></div>
                            </div>
                            <span class="ml-4 text-[9px] font-black text-gray-300 uppercase">/ 486H</span>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openLogModal({{ $student->id }}, '{{ addslashes($student->name) }}', {{ json_encode($student->timeLogs->sortByDesc('date')->values()->map(function($log) {
                                $totalHours = $log->total_hours ?? 0;
                                $renderedHours = $totalHours > 1 ? $totalHours - 1 : $totalHours;
                                $h = floor($totalHours);
                                $m = round(($totalHours - $h) * 60);
                                $rh = floor($renderedHours);
                                $rm = round(($renderedHours - $rh) * 60);

                                return [
                                    'date' => \Carbon\Carbon::parse($log->date)->format('M d, Y'),
                                    'time_in' => \Carbon\Carbon::parse($log->time_in)->format('H:i'),
                                    'time_out' => $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('H:i') : 'LIVE',
                                    'total_hours' => $h . 'h ' . $m . 'm',
                                    'rendered_hours' => $rh . 'h ' . $rm . 'm',
                                    'status' => $log->status,
                                    'notes' => $log->notes ?: '—'
                                ];
                            })) }})" class="text-[10px] font-black uppercase tracking-widest border border-black px-4 py-2 bg-black text-white hover:bg-gray-800 transition-colors inline-block">
                                Full Log
                            </button>
                            <div class="relative group">
                                <button class="text-[10px] font-black uppercase tracking-widest border border-black px-4 py-2 bg-black text-white hover:bg-gray-800 transition-colors inline-flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Export <span>▼</span>
                                </button>
                                <div class="absolute right-0 mt-0 w-44 bg-white border border-black shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-10">
                                    <a href="{{ route('admin.students.export', ['user' => $student, 'type' => 'excel']) }}" class="block px-4 py-3 text-xs font-bold uppercase tracking-widest text-black hover:bg-gray-100 border-b border-gray-200">
                                        <svg class="h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Export Excel
                                    </a>
                                    <a href="{{ route('admin.students.export', ['user' => $student, 'type' => 'pdf']) }}" class="block px-4 py-3 text-xs font-bold uppercase tracking-widest text-black hover:bg-gray-100">
                                        <svg class="h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        Export PDF
                                    </a>
                                </div>
                            </div>
                            <form action="{{ route('admin.students.remove', $student) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this student? All logs will remain but they will no longer be assigned to this room.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[10px] font-black uppercase tracking-widest border border-red-500 text-red-500 px-4 py-2 hover:bg-red-500 hover:text-white transition-all">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-8 py-20 text-center text-gray-300 font-bold uppercase tracking-widest text-sm">No students currently assigned.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Student Log Modal -->
<div id="studentLogModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-4xl max-h-[90vh] flex flex-col relative border-2 border-black">
        <div class="p-8 border-b-2 border-black flex justify-between items-center bg-gray-50">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Time Log Audit</p>
                <h3 id="modalStudentName" class="text-3xl font-black uppercase tracking-tight text-black leading-none">Student Name</h3>
            </div>
            <button onclick="document.getElementById('studentLogModal').classList.add('hidden')" class="text-black hover:rotate-90 transition-transform p-2 border-2 border-black">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <div class="p-8">
            <table class="min-w-full text-left">
                <thead>
                    <tr class="border-b-2 border-black">
                        <th class="py-4 text-[10px] font-black text-black uppercase tracking-widest">Date / Session</th>
                        <th class="py-4 text-[10px] font-black text-black uppercase tracking-widest text-center">Duration</th>
                        <th class="py-4 text-[10px] font-black text-black uppercase tracking-widest text-center">Rendered</th>
                        <th class="py-4 text-[10px] font-black text-black uppercase tracking-widest text-center">Status</th>
                        <th class="py-4 text-[10px] font-black text-black uppercase tracking-widest">Registry Notes</th>
                    </tr>
                </thead>
                <tbody id="modalLogTableBody" class="divide-y divide-gray-100">
                    <!-- Rows injected by JS -->
                </tbody>
            </table>
        </div>

        <div class="px-8 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-widest">
                Showing <span id="paginationInfo">1-10</span> of <span id="paginationTotal">0</span>
            </div>
            <div class="flex gap-2">
                <button onclick="previousPage()" class="px-4 py-2 border border-black text-xs font-black uppercase tracking-widest hover:bg-black hover:text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed" id="prevBtn">
                    ← Previous
                </button>
                <div id="pageNumbers" class="flex gap-1"></div>
                <button onclick="nextPage()" class="px-4 py-2 border border-black text-xs font-black uppercase tracking-widest hover:bg-black hover:text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed" id="nextBtn">
                    Next →
                </button>
            </div>
        </div>

        <div class="p-6 border-t border-black bg-gray-50 text-right">
            <button onclick="document.getElementById('studentLogModal').classList.add('hidden')" class="bg-black text-white px-8 py-3 text-[10px] font-black uppercase tracking-widest hover:bg-gray-800">
                Close Audit
            </button>
        </div>
    </div>
</div>

<script>
    let allLogs = [];
    let currentPage = 1;
    const itemsPerPage = 10;

    function openLogModal(studentId, studentName, logs) {
        document.getElementById('modalStudentName').innerText = studentName;
        allLogs = logs;
        currentPage = 1;
        renderPage();
        document.getElementById('studentLogModal').classList.remove('hidden');
    }

    function renderPage() {
        const body = document.getElementById('modalLogTableBody');
        body.innerHTML = '';

        if (allLogs.length === 0) {
            body.innerHTML = '<tr><td colspan="5" class="py-20 text-center text-gray-300 font-bold uppercase text-sm tracking-widest">No logs recorded for this student.</td></tr>';
            document.getElementById('paginationInfo').innerText = '0-0';
            document.getElementById('paginationTotal').innerText = '0';
            document.getElementById('prevBtn').disabled = true;
            document.getElementById('nextBtn').disabled = true;
            document.getElementById('pageNumbers').innerHTML = '';
            return;
        }

        const totalPages = Math.ceil(allLogs.length / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, allLogs.length);
        const paginatedLogs = allLogs.slice(startIndex, endIndex);

        paginatedLogs.forEach(log => {
            const statusClass = log.status === 'active' ? 'bg-black text-white' : 'bg-gray-100 text-gray-400';
            body.innerHTML += `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-5">
                        <p class="text-[11px] font-black text-black uppercase">${log.date}</p>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">${log.time_in} - ${log.time_out}</p>
                    </td>
                    <td class="py-5 text-center text-xs font-black text-black tabular-nums">
                        ${log.total_hours}
                    </td>
                    <td class="py-5 text-center text-xs font-black text-black tabular-nums">
                        ${log.rendered_hours}
                    </td>
                    <td class="py-5 text-center">
                        <span class="text-[8px] font-black px-2 py-0.5 uppercase tracking-tighter ${statusClass}">
                            ${log.status}
                        </span>
                    </td>
                    <td class="py-5 text-[10px] font-bold text-gray-400 uppercase tracking-tight max-w-xs truncate">
                        ${log.notes}
                    </td>
                </tr>
            `;
        });

        // Update pagination info
        document.getElementById('paginationInfo').innerText = `${startIndex + 1}-${endIndex}`;
        document.getElementById('paginationTotal').innerText = allLogs.length;

        // Update button states
        document.getElementById('prevBtn').disabled = currentPage === 1;
        document.getElementById('nextBtn').disabled = currentPage === totalPages;

        // Render page numbers
        const pageNumbersDiv = document.getElementById('pageNumbers');
        pageNumbersDiv.innerHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = i === currentPage ? 
                'px-2 py-1 bg-black text-white text-xs font-bold border border-black' : 
                'px-2 py-1 border border-gray-300 text-xs font-bold text-black hover:border-black transition-all';
            btn.onclick = () => goToPage(i);
            pageNumbersDiv.appendChild(btn);
        }
    }

    function previousPage() {
        if (currentPage > 1) {
            currentPage--;
            renderPage();
        }
    }

    function nextPage() {
        const totalPages = Math.ceil(allLogs.length / itemsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            renderPage();
        }
    }

    function goToPage(page) {
        currentPage = page;
        renderPage();
    }
</script>

<!-- Assign Student Modal -->
<div id="assignStudentModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg p-10 relative">
        <button onclick="document.getElementById('assignStudentModal').classList.add('hidden')" class="absolute top-6 right-6 text-black hover:rotate-90 transition-transform">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
        
        <h3 class="text-3xl font-black uppercase tracking-tight mb-8">Assign to Room</h3>
        
        <form action="{{ route('admin.students.assign') }}" method="POST">
            @csrf
            <input type="hidden" name="company_id" value="{{ $company->id }}">
            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Select Candidate</label>
                    <select name="user_id" required class="w-full p-4 border border-gray-200 focus:border-black outline-none font-bold uppercase text-sm appearance-none">
                        <option value="">CHOOSE A STUDENT...</option>
                        @foreach(App\Models\User::where('role', 'student')->whereNull('company_id')->orderBy('name')->get() as $student)
                            <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="pt-6">
                    <button type="submit" class="w-full py-5 bg-black text-white font-black uppercase tracking-widest hover:bg-gray-800 transition-all">
                        Confirm Assignment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
