@extends('layouts.app')

@section('header')
<h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Konfirmasi Pembelian</h1>
@endsection

@section('content')
@if($errors->any())
<script>showNotification("{{ $errors->first() }}", 'error');</script>
@endif

@if(session('status'))
<script>showNotification("{{ session('status') }}", 'success');</script>
@endif

<div class="container px-4 mx-auto" x-data="saleForm()" x-init="init()">
    <form id="sales-form" action="{{ route('employee.sales.store') }}" method="POST" @submit.prevent="submitForm" class="max-w-6xl mx-auto">
        @csrf
        <div class="p-6 bg-white shadow-lg rounded-xl dark:bg-gray-800">
            <div class="flex flex-col gap-8 md:flex-row">
                <!-- Product List -->
                <div class="flex-1">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-gray-100">Produk Dipilih</h2>
                    <div class="space-y-4 h-[400px] overflow-y-auto pr-2">
                        @foreach ($selectedProducts as $product)
                        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                            <div class="flex items-center gap-4">
                                <img src="{{ asset('storage/' . $product['image']) }}" alt="{{ $product['name'] }}" class="object-cover w-16 h-16 rounded-lg">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $product['name'] }}
                                        <input type="hidden" name="products[{{ $product['id'] }}][id]" value="{{ $product['id'] }}">
                                        <input type="hidden" name="products[{{ $product['id'] }}][quantity]" value="{{ $product['quantity'] }}">
                                    </h3>
                                    <div class="grid grid-cols-2 gap-2 mt-2 text-sm">
                                        <div class="text-gray-600 dark:text-gray-300">Harga: Rp {{ number_format($product['price'], 0, ',', '.') }}</div>
                                        <div class="text-gray-600 dark:text-gray-300">Qty: {{ $product['quantity'] }}</div>
                                        <div class="col-span-2 font-medium text-blue-600 dark:text-blue-400">
                                            Subtotal: Rp {{ number_format($product['subtotal'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="p-3 mt-4 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                        <p class="text-lg font-bold text-blue-700 dark:text-blue-300">Total: Rp {{ number_format($totalPrice, 0, ',', '.') }}</p>
                        <div x-show="pointsUsed > 0">
                            <p class="mt-2 text-sm text-blue-600 dark:text-blue-300">Diskon Poin: Rp <span x-text="(pointsUsed * 1000).toLocaleString('id-ID')"></span></p>
                            <p class="text-lg font-bold text-blue-700 dark:text-blue-300">
                                Total Setelah Diskon: Rp <span x-text="(totalPrice - (pointsUsed * 1000)).toLocaleString('id-ID')"></span>
                            </p>
                        </div>
                        <div x-show="showPointsInfo" class="mt-2 text-sm text-blue-600 dark:text-blue-300">
                            Poin yang akan didapat: <span x-text="pointsEarned"></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="md:w-96">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-gray-100">Pembayaran</h2>
                    <div class="space-y-5">
                        <!-- Customer Status -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Status Pelanggan</label>
                            <select name="status" x-model="customerStatus" @change="toggleMemberFields()" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                <option value="non-member">Non-Member</option>
                                <option value="member">Member</option>
                            </select>
                        </div>

                        <!-- Member Fields -->
                        <div x-show="showMemberFields" class="space-y-3">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Telepon Member</label>
                                <input type="text" name="phone" x-model="memberPhone" @input.debounce.500ms="checkMember()" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" placeholder="08xxxxxxxxxx">
                            </div>

                            <div x-show="showMemberInfo" class="p-2 rounded bg-green-50 dark:bg-green-900/20">
                                <p class="text-sm text-green-700 dark:text-green-300">
                                    Nama: <span x-text="memberName"></span><br>
                                    Poin: <span x-text="memberPoints"></span>
                                </p>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Gunakan Poin (Tersedia: <span x-text="memberPoints"></span>)
                                    </label>
                                    <input type="number" name="points_used" x-model.number="pointsUsed" :max="maxPointsUsable" min="0" @input="updatePointsUsed()" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" placeholder="Masukkan poin yang digunakan">
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Maksimum poin yang dapat digunakan: <span x-text="maxPointsUsable"></span> (1 Poin = Rp 1.000)
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Name -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Nama Pelanggan</label>
                            <input type="text" name="customer_name" x-model="customerName" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" placeholder="Masukkan nama">
                        </div>

                        <!-- Payment Amount -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Bayar</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <span class="px-2 py-1 font-bold text-blue-500 bg-blue-100 rounded-l-md dark:bg-blue-900 dark:text-blue-300">Rp</span>
                                </div>
                                <input type="text" name="amount_paid" x-model="amountPaid" @input="formatCurrency()" required class="pl-20 w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" placeholder="0">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full px-6 py-3 font-semibold text-white transition-all duration-200 bg-blue-600 rounded-lg hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800">
                            Proses Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Konfirmasi Modal untuk Pendaftaran Member -->
    <div x-show="showConfirmationModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50" x-transition>
        <div class="w-full max-w-md bg-white rounded-lg shadow-xl dark:bg-gray-800">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Konfirmasi Pendaftaran Member</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300" x-text="confirmationMessage"></p>
                <div class="flex justify-end mt-6 space-x-3">
                    <button @click="cancelRegistration()" type="button" class="px-4 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">Batal</button>
                    <button @click="confirmRegistration()" type="button" class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">Ya, Daftarkan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function saleForm() {
        return {
            customerStatus: 'non-member',
            memberPhone: '',
            customerName: '',
            amountPaid: '',
            showMemberFields: false,
            showMemberInfo: false,
            showPointsInfo: false,
            showConfirmationModal: false,
            confirmationMessage: '',
            memberName: '-',
            memberPoints: 0,
            pointsEarned: 0,
            totalPrice: {{ $totalPrice }},
            pointsUsed: 0,
            maxPointsUsable: 0,

            init() {
                this.toggleMemberFields();
                this.calculatePoints();
                if (this.customerStatus === 'member' && this.memberPhone) {
                    this.checkMember();
                }
            },

            toggleMemberFields() {
                this.showMemberFields = this.customerStatus === 'member';
                this.showPointsInfo = this.customerStatus === 'member';
                if (!this.showMemberFields) {
                    this.resetMemberFields();
                }
            },

            submitForm() {
                if(this.customerStatus === 'member' && !this.memberPhone) {
                    showNotification('Nomor telepon member harus diisi', 'error');
                    return;
                }

                const form = document.getElementById('sales-form');
                const formData = new FormData(form);
                const amountPaid = formData.get('amount_paid').replace(/[^0-9]/g, '');
                formData.set('amount_paid', amountPaid);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else if (data.needs_confirmation) {
                        this.showConfirmationModal = true;
                        this.confirmationMessage = data.message;
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Terjadi kesalahan sistem.', 'error');
                });
            },

            checkMember() {
                if (this.memberPhone.length < 10) {
                    this.showMemberInfo = false;
                    return;
                }

                fetch('{{ route("member.sales.check-member") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ phone: this.memberPhone })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.exists) {
                            this.showMemberInfo = true;
                            this.memberName = data.member.name;
                            this.memberPoints = Number(data.member.points);
                            this.customerName = data.member.name;
                            this.calculateMaxPoints();
                        } else {
                            this.showConfirmationModal = true;
                            this.confirmationMessage = 'Nomor tidak terdaftar. Jadikan akun Anda sebagai member?';
                        }
                    }
                });
            },

            calculateMaxPoints() {
                const discountPerPoint = 1000;
                const maxBasedOnTotal = Math.floor(this.totalPrice / discountPerPoint);
                this.maxPointsUsable = Math.min(this.memberPoints, maxBasedOnTotal);
            },

            updatePointsUsed() {
                if (this.pointsUsed > this.maxPointsUsable) {
                    this.pointsUsed = this.maxPointsUsable;
                }
                if (this.pointsUsed < 0) {
                    this.pointsUsed = 0;
                }
            },

            confirmRegistration() {
                fetch('{{ route("member.sales.register-member") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        phone: this.memberPhone,
                        user_id: {{ auth()->id() }}
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.showMemberInfo = true;
                        this.memberName = data.member.name;
                        this.memberPoints = data.member.points;
                        this.customerName = data.member.name;
                        this.showConfirmationModal = false;
                        this.calculateMaxPoints(); // Tambahkan ini
                        showNotification('Berhasil terdaftar sebagai member!', 'success');
                    } else {
                        showNotification(data.message || 'Pendaftaran gagal', 'error');
                    }
                })
                .catch(error => {
                    showNotification('Terjadi kesalahan saat mendaftarkan member', 'error');
                });
            },

            cancelRegistration() {
                this.showConfirmationModal = false;
                this.memberPhone = '';
                this.customerStatus = 'non-member';
                this.toggleMemberFields();
            },

            calculatePoints() {
                this.pointsEarned = Math.floor(this.totalPrice / 10000).toString();
            },

            formatCurrency() {
                let value = this.amountPaid.replace(/[^0-9]/g, '');
                if (value) {
                    value = parseInt(value, 10).toLocaleString('id-ID');
                    this.amountPaid = value;
                }
            },

            resetMemberFields() {
                this.memberPhone = '';
                this.showMemberInfo = false;
                this.memberName = '-';
                this.memberPoints = null;
            }
        }
    }

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed p-4 text-white rounded-lg shadow-lg top-4 right-4 ${
            type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 5000);
    }
</script>
@endsection
