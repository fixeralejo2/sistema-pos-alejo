<?php
namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CashRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver caja')->only(['index', 'show']);
        $this->middleware('permission:abrir caja')->only(['create', 'store']);
        $this->middleware('permission:cerrar caja')->only(['close', 'doClose']);
    }

    public function index()
    {
        $registers = CashRegister::with('user')->latest()->paginate(15);
        $openRegister = CashRegister::where('user_id', Auth::id())->where('status', 'open')->latest()->first();
        return view('cash-registers.index', compact('registers', 'openRegister'));
    }

    public function create()
    {
        $existingOpen = CashRegister::where('user_id', Auth::id())->where('status', 'open')->first();
        if ($existingOpen) {
            return redirect()->route('cash-registers.index')->with('warning', 'Ya tienes una caja abierta.');
        }
        return view('cash-registers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'opening_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        CashRegister::create([
            'user_id' => Auth::id(),
            'opening_amount' => $validated['opening_amount'],
            'status' => 'open',
            'notes' => $validated['notes'] ?? null,
            'opened_at' => Carbon::now(),
        ]);

        return redirect()->route('cash-registers.index')->with('success', 'Caja abierta exitosamente.');
    }

    public function show(CashRegister $cashRegister)
    {
        $cashRegister->load(['user', 'sales.items.product']);

        $salesByMethod = $cashRegister->sales()
            ->whereNotIn('status', ['cancelada', 'anulada'])
            ->selectRaw('payment_method, SUM(total) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        $totalSales = $cashRegister->sales()
            ->whereNotIn('status', ['cancelada', 'anulada'])
            ->sum('total');

        return view('cash-registers.show', compact('cashRegister', 'salesByMethod', 'totalSales'));
    }

    public function close(CashRegister $cashRegister)
    {
        if ($cashRegister->status === 'closed') {
            return redirect()->route('cash-registers.index')->with('warning', 'Esta caja ya está cerrada.');
        }

        $salesByMethod = $cashRegister->sales()
            ->whereNotIn('status', ['cancelada', 'anulada'])
            ->selectRaw('payment_method, SUM(total) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        $totalSales = $cashRegister->sales()
            ->whereNotIn('status', ['cancelada', 'anulada'])
            ->sum('total');

        return view('cash-registers.close', compact('cashRegister', 'salesByMethod', 'totalSales'));
    }

    public function doClose(Request $request, CashRegister $cashRegister)
    {
        $validated = $request->validate([
            'closing_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $cashRegister->update([
            'closing_amount' => $validated['closing_amount'],
            'status' => 'closed',
            'notes' => ($cashRegister->notes ? $cashRegister->notes . "\n" : '') . ($validated['notes'] ?? ''),
            'closed_at' => Carbon::now(),
        ]);

        return redirect()->route('cash-registers.index')->with('success', 'Caja cerrada exitosamente.');
    }
}
