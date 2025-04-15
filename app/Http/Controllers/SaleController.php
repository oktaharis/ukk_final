<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class SaleController extends Controller
{
    // Menampilkan daftar transaksi
    public function index()
    {
        $sales = Sale::with(['user', 'products'])->paginate(5);
        return view('admin.sales.index', compact('sales'));
    }
    public function indexPtEmployee()
    {
        $sales = Sale::with(['user', 'products'])
            ->when(request('search'), function ($q) {
                $q->where('customer_name', 'like', '%' . request('search') . '%')
                  ->orWhere('id', 'like', '%' . request('search') . '%');
            })
            ->when(request('filter') == 'daily' && request('date'), function ($q) {
                $q->whereDate('created_at', request('date'));
            })
            ->when(request('filter') == 'monthly' && request('date'), function ($q) {
                $q->whereMonth('created_at', date('m', strtotime(request('date'))))
                  ->whereYear('created_at', date('Y', strtotime(request('date'))));
            })
            ->when(request('filter') == 'yearly' && request('date'), function ($q) {
                $q->whereYear('created_at', request('date'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5)
            ->appends(request()->query());

        return view('employee.sales.index', compact('sales'));
    }

    public function export(Request $request)
    {
        return Excel::download(
            new SalesExport(
                $request->filter,    // Ambil dari request
                $request->date,     // Ambil dari request
                $request->search     // Ambil dari request
            ),
            'sales.xlsx'
        );
    }

    // Menampilkan form tambah transaksi
    public function createPtEmployee()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('employee.sales.create', compact('products'));
    }
    public function confirmSale(Request $request)
    {
        // dd($request->all());
        $filteredProducts = array_filter($request->products, function ($item) {
            return isset($item['quantity']) && $item['quantity'] >= 1;
        });

        if (empty($filteredProducts)) {
            return back()->withErrors(['products' => 'Pilih minimal 1 produk.']);
        }

        // Ganti data request dengan produk yang valid
        $request->merge(['products' => $filteredProducts]);
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $selectedProducts = collect($request->products)->map(function ($item) {
            $product = Product::findOrFail($item['id']);
            if ($product->stock < $item['quantity']) {
                throw ValidationException::withMessages(['products' => "Stok produk {$product->name} tidak mencukupi."]);
            }
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $item['quantity'],
                'subtotal' => $product->price * $item['quantity'],
                'image' => $product->image,
            ];
        });

        $totalPrice = $selectedProducts->sum('subtotal');

        return view('employee.sales.confirm', compact('selectedProducts', 'totalPrice'));
    }
    public function storeSale(Request $request)
    {
        try {
            DB::beginTransaction();

            // Clean number format and validate
            $request->merge(['amount_paid' => str_replace('.', '', $request->amount_paid)]);

            $validator = Validator::make($request->all(), [
                'products' => 'required|array|min:1',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
                'status' => 'required|in:member,non-member',
                'amount_paid' => 'required|numeric|min:0',
                'customer_name' => 'required|string|max:255',
                'phone' => $request->status === 'member' ? 'required|numeric|digits_between:10,15' : '',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            // Calculate total price and check stock
            $originalTotalPrice = 0;
            $products = [];

            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['id']);

                if ($product->stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stok produk {$product->name} tidak mencukupi!"
                    ], 422);
                }

                $originalTotalPrice += $product->price * $item['quantity'];
                $products[$product->id] = ['quantity' => $item['quantity']];
            }

            $totalPrice = $originalTotalPrice;
            $discount = 0;
            $pointsUsed = 0;
            $pointsEarned = 0;

            // Process member-specific logic
            if ($request->status === 'member') {
                $member = User::where('phone', $request->phone)->first();

                if (!$member) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'needs_confirmation' => true,
                        'message' => 'Nomor tidak terdaftar. Jadikan akun Anda sebagai member?'
                    ], 422);
                }

                // Process points if used
                $pointsUsed = $request->points_used ?? 0;
                if ($pointsUsed > 0) {
                    if ($pointsUsed > $member->points) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Poin yang digunakan melebihi poin yang dimiliki.'
                        ], 422);
                    }

                    $discount = $pointsUsed * 1000;
                    $totalPrice = max($originalTotalPrice - $discount, 0);
                }

                $pointsEarned = floor($originalTotalPrice / 10000);
            }

            // Validate payment amount
            if ($request->amount_paid < $totalPrice) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah pembayaran kurang dari total harga.'
                ], 422);
            }

            // Create transaction
            $saleData = [
                'user_id' => Auth::id(),
                'total_price' => $totalPrice,
                'discount' => $discount,
                'final_price' => $totalPrice,
                'amount_paid' => $request->amount_paid,
                'change' => $request->amount_paid - $totalPrice,
                'status' => $request->status,
                'customer_name' => $request->customer_name,
                'points_used' => $pointsUsed,
                'phone' => $request->status === 'member' ? $request->phone : null,
            ];

            $sale = Sale::create($saleData);

            // Update stock and product relations
            foreach ($products as $productId => $data) {
                $product = Product::find($productId);
                $product->decrement('stock', $data['quantity']);
                $sale->products()->attach($productId, $data);
            }

            // Process points for member
            if ($request->status === 'member') {
                $member->decrement('points', $pointsUsed);
                $member->increment('points', $pointsEarned);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect' => route('employee.sales.detail', ['id' => $sale->id]),
                'message' => 'Transaksi berhasil diproses!',
                'points_earned' => $pointsEarned,
                'discount_applied' => $discount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Menampilkan detail transaksi
    public function show(Sale $sale)
    {
        $sale->load(['products', 'user']);
        return view('employee.sales.show', compact('sale'));
    }
    public function detail($id)
    {
        $sale = Sale::with('products')->findOrFail($id);
        return view('employee.sales.detail', compact('sale'));
    }

    // Menghapus transaksi
    public function destroy(Sale $sale)
    {
        // Kembalikan stok produk
        foreach ($sale->products as $product) {
            $product->stock += $product->pivot->quantity;
            $product->save();
        }

        $sale->delete();
        return redirect()->route('admin.sales.index')->with('success', 'Transaksi berhasil dihapus.');
    }
    public function exportPdfPerSale($id) // Terima ID penjualan sebagai parameter
    {
        // Ambil data penjualan berdasarkan ID
        $sale = Sale::with(['products', 'user'])->findOrFail($id);

        // Load view dan kirim data penjualan
        $pdf = Pdf::loadView('exports.single_sale_pdf', [
            'sale' => $sale // Kirim data penjualan ke view
        ]);

        // Download PDF dengan nama file yang sesuai
        return $pdf->download("invoice-{$sale->id}.pdf");
    }
    public function exportPdf(Sale $sale)
    {
        // dd($sale);
        $sale->load(['products', 'user']); // Eager loading

        $pdf = Pdf::loadView('exports.single_sale_pdf', [
            'sale' => $sale // Kirim single sale ke view
        ]);

        return $pdf->download("invoice-{$sale->id}.pdf");
    }
}
