<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionApproval;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('transactions.index');
    }

    /**
     * Display transactions that need approval
     */
    public function pending()
    {
        return view('transactions.pending');
    }

    /**
     * Get transactions data for datatable (AJAX)
     */
    public function getData(Request $request)
    {
        try {
            $query = Transaction::with(['user', 'approvals.user']);

            // Filter by user role
            if (Auth::user()->isPemohon()) {
                $query->where('user_id', Auth::id());
            }

            // Search
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_transaksi', 'like', "%{$search}%")
                        ->orWhere('nama_pemohon', 'like', "%{$search}%")
                        ->orWhere('nama_perusahaan', 'like', "%{$search}%")
                        ->orWhere('uraian_transaksi', 'like', "%{$search}%");
                });
            }

            // Ordering
            if ($request->has('order')) {
                $columns = ['id', 'nomor_transaksi', 'nama_pemohon', 'tanggal_pengajuan', 'total', 'status'];
                $columnIndex = $request->order[0]['column'];
                $columnName = $columns[$columnIndex] ?? 'id';
                $direction = $request->order[0]['dir'];
                $query->orderBy($columnName, $direction);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Pagination
            $recordsTotal = Transaction::count();
            $recordsFiltered = $query->count();

            if ($request->has('start') && $request->has('length')) {
                $query->skip($request->start)->take($request->length);
            }

            $transactions = $query->get();

            $data = $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'nomor_transaksi' => $transaction->nomor_transaksi,
                    'nama_pemohon' => $transaction->nama_pemohon,
                    'nama_perusahaan' => $transaction->nama_perusahaan,
                    'tanggal_pengajuan' => $transaction->tanggal_pengajuan->format('d/m/Y'),
                    'uraian_transaksi' => $transaction->uraian_transaksi,
                    'total' => 'Rp ' . number_format($transaction->total, 0, ',', '.'),
                    'status' => $transaction->status,
                    'status_label' => $this->getStatusLabel($transaction->status),
                    'can_approve' => $transaction->canBeApprovedBy(Auth::user()->role),
                    'is_backward_flow' => $transaction->isBackwardFlow(),
                    'created_at' => $transaction->created_at->format('d/m/Y H:i'),
                ];
            });

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending transactions data for datatable (AJAX)
     */
    public function getPendingData(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Filter hanya transaksi yang perlu approval oleh user saat ini
            $query = Transaction::with(['user', 'approvals.user'])
                ->where(function($q) use ($user) {
                    // Get transactions that can be approved by current user
                    $q->whereHas('approvals', function($approvalQuery) use ($user) {
                        $approvalQuery->where('role', $user->role)
                            ->where('status', 'pending');
                    });
                });

            // Search
            if ($request->has('search') && $request->search['value'] !== '') {
                $search = $request->search['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_transaksi', 'like', "%{$search}%")
                        ->orWhere('nama_pemohon', 'like', "%{$search}%")
                        ->orWhere('nama_perusahaan', 'like', "%{$search}%")
                        ->orWhere('uraian_transaksi', 'like', "%{$search}%");
                });
            }

            // Ordering
            if ($request->has('order')) {
                $columns = ['id', 'nomor_transaksi', 'nama_pemohon', 'tanggal_pengajuan', 'total', 'status'];
                $columnIndex = $request->order[0]['column'];
                $columnName = $columns[$columnIndex] ?? 'id';
                $direction = $request->order[0]['dir'];
                $query->orderBy($columnName, $direction);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Pagination
            $recordsTotal = Transaction::where(function($q) use ($user) {
                $q->whereHas('approvals', function($approvalQuery) use ($user) {
                    $approvalQuery->where('role', $user->role)
                        ->where('status', 'pending');
                });
            })->count();
            
            $recordsFiltered = $query->count();

            if ($request->has('start') && $request->has('length')) {
                $query->skip($request->start)->take($request->length);
            }

            $transactions = $query->get();

            // Filter transactions that actually can be approved
            $transactions = $transactions->filter(function($transaction) use ($user) {
                return $transaction->canBeApprovedBy($user->role);
            });

            $data = $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'nomor_transaksi' => $transaction->nomor_transaksi,
                    'nama_pemohon' => $transaction->nama_pemohon,
                    'nama_perusahaan' => $transaction->nama_perusahaan,
                    'tanggal_pengajuan' => $transaction->tanggal_pengajuan->format('d/m/Y'),
                    'uraian_transaksi' => $transaction->uraian_transaksi,
                    'total' => 'Rp ' . number_format($transaction->total, 0, ',', '.'),
                    'status' => $transaction->status,
                    'status_label' => $this->getStatusLabel($transaction->status),
                    'can_approve' => true, // Always true in pending list
                    'is_backward_flow' => $transaction->isBackwardFlow(),
                    'created_at' => $transaction->created_at->format('d/m/Y H:i'),
                ];
            })->values(); // Reset array keys

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('transactions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_pemohon' => 'required|string|max:255',
                'nama_perusahaan' => 'required|string|max:255',
                'tanggal_pengajuan' => 'required|date',
                'uraian_transaksi' => 'required|string',
                'total' => 'required|numeric|min:0',
                'dasar_transaksi' => 'nullable|string',
                'lawan_transaksi' => 'nullable|string|max:255',
                'rekening_transaksi' => 'nullable|string|max:255',
                'rencana_tanggal_transaksi' => 'nullable|date',
                'pengakuan_transaksi' => 'nullable|string|max:255',
                'keterangan' => 'nullable|string',
                'lampiran_dokumen' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
            ]);

            DB::beginTransaction();

            $validated['user_id'] = Auth::id();
            $validated['status'] = 'draft';

            // Handle file upload
            if ($request->hasFile('lampiran_dokumen')) {
                $file = $request->file('lampiran_dokumen');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('transactions', $filename, 'public');
                $validated['lampiran_dokumen'] = $path;
            }

            $transaction = Transaction::create($validated);

            // Create approval records for all pejabat
            $pejabatRoles = ['pejabat_1', 'pejabat_2', 'pejabat_3', 'pejabat_4'];
            foreach ($pejabatRoles as $role) {
                $pejabat = User::where('role', $role)->first();
                if ($pejabat) {
                    TransactionApproval::create([
                        'transaction_id' => $transaction->id,
                        'user_id' => $pejabat->id,
                        'role' => $role,
                        'status' => 'pending',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat',
                'data' => $transaction,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'approvals.user']);
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Get single transaction data (AJAX)
     */
    public function getDetail($id)
    {
        try {
            $transaction = Transaction::with(['user', 'approvals.user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $transaction->id,
                    'nomor_transaksi' => $transaction->nomor_transaksi,
                    'nama_pemohon' => $transaction->nama_pemohon,
                    'nama_perusahaan' => $transaction->nama_perusahaan,
                    'tanggal_pengajuan' => $transaction->tanggal_pengajuan->format('Y-m-d'),
                    'uraian_transaksi' => $transaction->uraian_transaksi,
                    'total' => $transaction->total,
                    'dasar_transaksi' => $transaction->dasar_transaksi,
                    'lawan_transaksi' => $transaction->lawan_transaksi,
                    'rekening_transaksi' => $transaction->rekening_transaksi,
                    'rencana_tanggal_transaksi' => $transaction->rencana_tanggal_transaksi ? $transaction->rencana_tanggal_transaksi->format('Y-m-d') : null,
                    'pengakuan_transaksi' => $transaction->pengakuan_transaksi,
                    'keterangan' => $transaction->keterangan,
                    'status' => $transaction->status,
                    'lampiran_dokumen' => $transaction->lampiran_dokumen,
                    'approvals' => $transaction->approvals,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        // Only allow editing if status is draft or dilengkapi
        if (!in_array($transaction->status, ['draft', 'dilengkapi']) || $transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit transaksi ini.');
        }

        // Load approvals with user to show completion request info
        $transaction->load(['approvals.user']);

        return view('transactions.edit', compact('transaction'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        try {
            // Only allow updating if status is draft or dilengkapi and current user is owner
            if (!in_array($transaction->status, ['draft', 'dilengkapi']) || $transaction->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengedit transaksi ini.',
                ], 403);
            }

            $validated = $request->validate([
                'nama_pemohon' => 'required|string|max:255',
                'nama_perusahaan' => 'required|string|max:255',
                'tanggal_pengajuan' => 'required|date',
                'uraian_transaksi' => 'required|string',
                'total' => 'required|numeric|min:0',
                'dasar_transaksi' => 'nullable|string',
                'lawan_transaksi' => 'nullable|string|max:255',
                'rekening_transaksi' => 'nullable|string|max:255',
                'rencana_tanggal_transaksi' => 'nullable|date',
                'pengakuan_transaksi' => 'nullable|string|max:255',
                'keterangan' => 'nullable|string',
                'lampiran_dokumen' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
            ]);

            DB::beginTransaction();

            // Handle file upload
            if ($request->hasFile('lampiran_dokumen')) {
                // Delete old file if exists
                if ($transaction->lampiran_dokumen) {
                    Storage::disk('public')->delete($transaction->lampiran_dokumen);
                }

                $file = $request->file('lampiran_dokumen');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('transactions', $filename, 'public');
                $validated['lampiran_dokumen'] = $path;
            }

            // Check if transaction was in dilengkapi status before update
            $wasDilengkapi = $transaction->status === 'dilengkapi';
            
            $transaction->update($validated);

            // If status was "dilengkapi", handle re-submission after completion
            if ($wasDilengkapi) {
                // Find the last pejabat who requested completion
                $lastPejabat = $transaction->approvals()
                    ->where('status', 'pending')
                    ->whereNotNull('catatan')
                    ->latest()
                    ->first();

                // Determine which pejabat to route back to
                $targetRole = $lastPejabat ? $lastPejabat->role : 'pejabat_2';
                
                // Reset the approval record for that pejabat so they can review again
                $targetApproval = $transaction->approvals()
                    ->where('role', $targetRole)
                    ->where('status', 'approved')
                    ->first();
                    
                if ($targetApproval) {
                    $targetApproval->update([
                        'status' => 'pending',
                        'catatan' => null,
                        'tanggal_approval' => null,
                    ]);
                }

                // Update status based on which pejabat requested completion
                // Set to the intermediate status so next approval moves it forward correctly
                // Also clear alasan_penolakan since data has been completed
                if ($targetRole === 'pejabat_2') {
                    $transaction->update([
                        'status' => 'pemeriksaan_tahap_2',
                        'alasan_penolakan' => null,
                    ]);
                } elseif ($targetRole === 'pejabat_3') {
                    $transaction->update([
                        'status' => 'menunggu_pejabat_3',
                        'alasan_penolakan' => null,
                    ]);
                }

                // Create approval record for data completion
                TransactionApproval::create([
                    'transaction_id' => $transaction->id,
                    'user_id' => Auth::id(),
                    'role' => 'pejabat_1', // Pemohon's role for tracking
                    'status' => 'completed',
                    'catatan' => 'Data telah dilengkapi sesuai permintaan dari ' . ($lastPejabat ? $lastPejabat->user->name : 'pejabat'),
                    'tanggal_approval' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diperbarui',
                'data' => $transaction,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        try {
            // Only allow deleting if status is draft and user is owner
            if ($transaction->status !== 'draft' || $transaction->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus transaksi ini.',
                ], 403);
            }

            DB::beginTransaction();

            // Delete file if exists
            if ($transaction->lampiran_dokumen) {
                Storage::disk('public')->delete($transaction->lampiran_dokumen);
            }

            $transaction->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Submit transaction for approval
     */
    public function submit(Transaction $transaction)
    {
        try {
            if ($transaction->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi sudah diajukan sebelumnya.',
                ], 400);
            }

            if ($transaction->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengajukan transaksi ini.',
                ], 403);
            }

            DB::beginTransaction();

            $transaction->update(['status' => 'menunggu_pejabat_1']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diajukan untuk persetujuan Pejabat 1',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengajukan transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve transaction by current pejabat
     */
    public function approve(Request $request, Transaction $transaction)
    {
        try {
            $user = Auth::user();

            if (!$user->isPejabat()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menyetujui transaksi.',
                ], 403);
            }

            if (!$transaction->canBeApprovedBy($user->role)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak dalam tahap persetujuan Anda.',
                ], 400);
            }

            $validated = $request->validate([
                'catatan' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // Update approval record
            $approval = $transaction->getApprovalByRole($user->role);
            if ($approval) {
                $approval->update([
                    'status' => 'approved',
                    'catatan' => $validated['catatan'] ?? null,
                    'tanggal_approval' => now(),
                ]);
            }

            // Update transaction status based on role
            $newStatus = $this->getNextStatusAfterApproval($transaction->status, $user->role);
            $transaction->update(['status' => $newStatus]);

            // Prepare success message based on flow type
            $message = 'Transaksi berhasil disetujui';
            
            // If pejabat 4 approves, mark as final approval
            if ($user->role === 'pejabat_4') {
                $transaction->update(['tanggal_disetujui' => now()]);
                $message = 'Transaksi berhasil disetujui. Formulir akan diteruskan ke Pejabat 3.';
            }
            
            // Backward flow messages
            if ($transaction->status === 'diinformasikan') {
                $message = 'Formulir berhasil diteruskan ke Pejabat 2 untuk disampaikan kepada Pemohon.';
            } elseif ($transaction->status === 'selesai') {
                $message = 'Proses approval selesai. Formulir yang telah disetujui telah siap untuk Pemohon.';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject transaction by current pejabat
     */
    public function reject(Request $request, Transaction $transaction)
    {
        try {
            $user = Auth::user();

            if (!$user->isPejabat()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menolak transaksi.',
                ], 403);
            }

            if (!$transaction->canBeApprovedBy($user->role)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak dalam tahap persetujuan Anda.',
                ], 400);
            }

            $validated = $request->validate([
                'alasan_penolakan' => 'required|string',
            ]);

            DB::beginTransaction();

            // Update approval record
            $approval = $transaction->getApprovalByRole($user->role);
            if ($approval) {
                $approval->update([
                    'status' => 'rejected',
                    'catatan' => $validated['alasan_penolakan'],
                    'tanggal_approval' => now(),
                ]);
            }

            // Update transaction status
            $transaction->update([
                'status' => 'ditolak',
                'alasan_penolakan' => $validated['alasan_penolakan'],
                'tanggal_ditolak' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil ditolak',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Request resubmission with completion
     */
    public function requestCompletion(Request $request, Transaction $transaction)
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'pejabat_2') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Pejabat 2 yang dapat meminta kelengkapan data.',
                ], 403);
            }

            $validated = $request->validate([
                'catatan' => 'required|string',
            ]);

            DB::beginTransaction();

            // Update approval record
            $approval = $transaction->getApprovalByRole($user->role);
            if ($approval) {
                $approval->update([
                    'status' => 'pending',
                    'catatan' => $validated['catatan'],
                ]);
            }

            // Update transaction status
            $transaction->update([
                'status' => 'dilengkapi',
                'alasan_penolakan' => $validated['catatan'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan kelengkapan berhasil dikirim',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal meminta kelengkapan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get next status after approval based on current status and role
     */
    private function getNextStatusAfterApproval($currentStatus, $role)
    {
        $statusFlow = [
            'menunggu_pejabat_1' => [
                'pejabat_1' => 'diskusi_pra_permohonan',
            ],
            'diskusi_pra_permohonan' => [
                'pejabat_2' => 'menunggu_pejabat_2',
            ],
            'menunggu_pejabat_2' => [
                'pejabat_2' => 'pemeriksaan_tahap_2',
            ],
            'pemeriksaan_tahap_2' => [
                'pejabat_2' => 'menunggu_pejabat_3',
            ],
            'menunggu_pejabat_3' => [
                'pejabat_3' => 'menunggu_pejabat_4',
            ],
            'menunggu_pejabat_4' => [
                'pejabat_4' => 'disetujui_pejabat_4',
            ],
            'disetujui_pejabat_4' => [
                'pejabat_3' => 'diinformasikan',
            ],
            'diinformasikan' => [
                'pejabat_2' => 'selesai',
            ],
        ];

        return $statusFlow[$currentStatus][$role] ?? $currentStatus;
    }

    /**
     * Get status label with badge color
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'menunggu_pejabat_1' => '<span class="badge bg-warning">Menunggu Pejabat 1</span>',
            'diskusi_pra_permohonan' => '<span class="badge bg-info">Diskusi Pra-Permohonan</span>',
            'menunggu_pejabat_2' => '<span class="badge bg-warning">Menunggu Pejabat 2</span>',
            'pemeriksaan_tahap_2' => '<span class="badge bg-info">Pemeriksaan Tahap 2</span>',
            'menunggu_pejabat_3' => '<span class="badge bg-warning">Menunggu Pejabat 3</span>',
            'dilengkapi' => '<span class="badge bg-primary">Dilengkapi</span>',
            'menunggu_pejabat_4' => '<span class="badge bg-warning">Menunggu Pejabat 4</span>',
            'disetujui_pejabat_4' => '<span class="badge bg-success">Disetujui Pejabat 4</span>',
            'diinformasikan' => '<span class="badge bg-info">Diinformasikan</span>',
            'selesai' => '<span class="badge bg-success">Selesai</span>',
            'ditolak' => '<span class="badge bg-danger">Ditolak</span>',
            'ajukan_ulang' => '<span class="badge bg-warning">Ajukan Ulang</span>',
        ];

        return $labels[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
}
