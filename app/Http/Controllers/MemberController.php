<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MemberController extends Controller
{
    public function checkMember(Request $request)
    {
        try {
            // Validasi nomor telepon harus berupa angka dan memiliki digit antara 10 sampai 15
            $request->validate([
                'phone' => 'required|numeric'
            ]);

            $member = User::where('phone', $request->phone)->first();
            $currentUser = Auth::user();

            return response()->json([
                'success'       => true,
                'exists'        => !!$member,
                // Untuk memastikan jika pengguna yang sedang login sudah memiliki nomor yang sama
                'is_current_user'=> $currentUser->phone === $request->phone,
                'member'        => $member ? [
                    'name'  => $member->name,
                    'points'=> $member->points
                ] : null,
                'message'       => $member ? 'Member ditemukan' : 'Member tidak terdaftar'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function registerMember(Request $request)
    {
        try {
            // Validasi: nomor harus unik (karena sebelumnya belum terdaftar) dan user_id harus valid
            $request->validate([
                'phone'   => 'required|numeric|unique:users,phone|digits_between:10,15',
                'user_id' => 'required|exists:users,id'
            ]);
            Log::info('Data transaksi:', [
                'register mmeber' => $request->all(),
                // 'sale' => $sale,
                // 'points' => $points,
            ]);


            DB::beginTransaction();

            $user = User::findOrFail($request->user_id);
            // Update user aktif dengan nomor telepon baru
            $user->update([
                'phone'  => $request->phone,
                // Pertahankan poin yang ada, atau bisa ditambah jika ada bonus pendaftaran
                'points' => $user->points
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'member'  => [
                    'name'  => $user->name,
                    'points'=> $user->points
                ]
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendaftarkan member: ' . $e->getMessage()
            ], 500);
        }
    }
}
