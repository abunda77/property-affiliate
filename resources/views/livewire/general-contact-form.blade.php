<div>
    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-600/20 border border-green-400/50 rounded-lg backdrop-blur-sm shadow-lg"
            role="alert" aria-live="polite">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-300 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-green-100 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid md:grid-cols-2 gap-8">
        <!-- Contact Information -->
        <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-8 shadow-2xl">
            <h2 class="text-2xl font-bold text-white mb-6">Informasi Kontak</h2>

            <div class="space-y-6">
                @if ($contactEmail)
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-500/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-semibold text-gray-300 mb-1">Email</h3>
                            <a href="mailto:{{ $contactEmail }}"
                                class="text-blue-400 hover:text-blue-300 transition-colors">
                                {{ $contactEmail }}
                            </a>
                        </div>
                    </div>
                @endif

                @if ($contactWhatsapp)
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-500/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-semibold text-gray-300 mb-1">WhatsApp</h3>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactWhatsapp) }}" target="_blank"
                                rel="noopener noreferrer" class="text-gray-400 hover:text-green-300 transition-colors">
                                {{ $contactWhatsapp }}
                            </a>
                        </div>
                    </div>
                @endif

                <div class="pt-6 border-t border-white/10">
                    <h3 class="text-sm font-semibold text-gray-300 mb-3">Jam Operasional</h3>
                    <div class="text-sm text-gray-400 space-y-1">
                        <p>Senin - Jumat: 09:00 - 17:00</p>
                        <p>Sabtu: 09:00 - 14:00</p>
                        <p>Minggu: Tutup</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-8 shadow-2xl">
            <h2 class="text-2xl font-bold text-white mb-6">Kirim Pesan</h2>

            <form wire:submit.prevent="submit" class="space-y-4" aria-label="Formulir kontak">
                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                        Nama Lengkap <span class="text-red-400" aria-label="wajib diisi">*</span>
                    </label>
                    <input type="text" id="name" wire:model="name"
                        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('name') border-red-500/50 @enderror"
                        placeholder="Masukkan nama lengkap Anda" aria-required="true">
                    @error('name')
                        <p class="mt-2 text-sm text-red-400" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                        Email <span class="text-red-400" aria-label="wajib diisi">*</span>
                    </label>
                    <input type="email" id="email" wire:model="email"
                        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('email') border-red-500/50 @enderror"
                        placeholder="nama@email.com" aria-required="true">
                    @error('email')
                        <p class="mt-2 text-sm text-red-400" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <!-- WhatsApp Field -->
                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-gray-300 mb-2">
                        Nomor WhatsApp <span class="text-red-400" aria-label="wajib diisi">*</span>
                    </label>
                    <input type="tel" id="whatsapp" wire:model="whatsapp"
                        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('whatsapp') border-red-500/50 @enderror"
                        placeholder="08123456789" aria-required="true">
                    @error('whatsapp')
                        <p class="mt-2 text-sm text-red-400" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subject Field -->
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-300 mb-2">
                        Subjek <span class="text-red-400" aria-label="wajib diisi">*</span>
                    </label>
                    <input type="text" id="subject" wire:model="subject"
                        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('subject') border-red-500/50 @enderror"
                        placeholder="Perihal pesan Anda" aria-required="true">
                    @error('subject')
                        <p class="mt-2 text-sm text-red-400" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message Field -->
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-300 mb-2">
                        Pesan <span class="text-red-400" aria-label="wajib diisi">*</span>
                    </label>
                    <textarea id="message" wire:model="message" rows="4"
                        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none @error('message') border-red-500/50 @enderror"
                        placeholder="Tulis pesan Anda di sini..." aria-required="true"></textarea>
                    @error('message')
                        <p class="mt-2 text-sm text-red-400" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-blue-800 disabled:opacity-50 text-white font-semibold py-3 px-6 rounded-lg transition-all flex items-center justify-center min-h-[48px] shadow-lg hover:shadow-blue-500/25"
                    aria-label="Kirim pesan">
                    <span wire:loading.remove class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Kirim Pesan
                    </span>
                    <span wire:loading class="flex items-center" role="status" aria-live="polite">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Mengirim...
                    </span>
                </button>

                <p class="text-xs text-gray-500 text-center">
                    Dengan mengirim formulir ini, Anda menyetujui untuk dihubungi oleh tim kami.
                </p>
            </form>
        </div>
    </div>
</div>
