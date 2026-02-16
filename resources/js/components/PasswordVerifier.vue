<script setup>
import { ref, computed } from 'vue';
import { usePassword } from '../composables/usePassword.js';

const hash = ref('');
const password = ref('');
const verificationResult = ref(null);
const hasChecked = ref(false);

const { verifyPassword, isLoading, error } = usePassword();

const resultColor = computed(() => {
    if (!hasChecked.value) return '';
    return verificationResult.value ? 'green' : 'red';
});

const handleVerify = async () => {
    if (!password.value || !hash.value) return;

    try {
        hasChecked.value = false;
        const result = await verifyPassword(password.value, hash.value);
        verificationResult.value = result.data.is_valid;
        hasChecked.value = true;
    } catch (err) {
        console.error('Verification failed:', err);
        hasChecked.value = false;
    }
};
</script>

<template>
    <div class="h-full bg-white/60 backdrop-blur-2xl border border-white/50 rounded-3xl shadow-xl shadow-emerald-500/5 p-6 flex flex-col justify-between transition-all duration-300">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="bg-emerald-500/10 p-2.5 rounded-2xl text-emerald-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800 leading-tight">Verify</h2>
                    <p class="text-xs font-semibold text-slate-400">Match Check</p>
                </div>
            </div>
        </div>

        <div class="space-y-6 flex-1">
            <!-- Hash Input First -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 pl-3">Bcrypt Hash</label>
                <div class="relative group">
                    <input
                        v-model="hash"
                        type="text"
                        placeholder="$2y$10$..."
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl py-3 px-4 text-xs font-mono text-slate-600 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm"
                    />
                </div>
            </div>

            <!-- Password Input Second -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 pl-3">Original Password</label>
                <div class="relative group">
                    <input
                        v-model="password"
                        type="text"
                        placeholder="Enter password..."
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl py-3 px-4 text-slate-700 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/10 transition-all text-lg font-medium shadow-sm"
                    />
                </div>
            </div>

            <!-- Verification Result -->
            <div v-if="hasChecked && !error" class="animate-fade-in-up">
                <div 
                    class="p-4 rounded-xl flex items-center justify-between shadow-sm transition-all duration-500 border"
                    :class="verificationResult 
                        ? 'bg-emerald-50 border-emerald-100 text-emerald-900' 
                        : 'bg-rose-50 border-rose-100 text-rose-900'"
                >
                    <div class="flex items-center gap-3">
                        <div class="p-1.5 bg-white rounded-lg shadow-sm">
                             <svg v-if="verificationResult" class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                             </svg>
                             <svg v-else class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                             </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold">
                                {{ verificationResult ? 'Match Confirmed' : 'Mismatch' }}
                            </h4>
                            <p class="text-[10px] font-medium opacity-80">
                                {{ verificationResult 
                                    ? 'Hash matches password.' 
                                    : 'Password does not match.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <button
            @click="handleVerify"
            :disabled="isLoading || !password || !hash"
            class="mt-6 w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-base rounded-[1rem] shadow-xl shadow-emerald-500/20 active:scale-[0.98] transition-all disabled:opacity-70 disabled:scale-100 disabled:cursor-not-allowed flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-500 group"
        >
             <div v-if="isLoading" class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
             <svg v-else class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
             </svg>
             <span>{{ isLoading ? 'Verifying...' : 'Verify Match' }}</span>
        </button>
        
        <p v-if="error" class="mt-3 text-center text-[10px] text-rose-500 font-bold bg-rose-50/50 p-2 rounded-lg border border-rose-100">
            {{ error }}
        </p>
    </div>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fade-in-up 0.5s ease-out forwards;
}
@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
