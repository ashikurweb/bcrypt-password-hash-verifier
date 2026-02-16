<script setup>
import { ref, computed } from 'vue';
import { usePassword } from '../composables/usePassword.js';

const password = ref('');
const rounds = ref(12);
const hashedResult = ref('');
const hasHashed = ref(false);
const copied = ref(false);
const blockedHashedResult = ref('');

    const { hashPassword, isLoading, error, copyToClipboard } = usePassword();

    const handleHash = async () => {
        if (!password.value) return;

        try {
            hasHashed.value = false;
            
            const result = await hashPassword(password.value, rounds.value);
            if (result && result.data && result.data.hashed_password) {
                hashedResult.value = result.data.hashed_password;
                hasHashed.value = true;
            } else {
                console.error('Invalid response structure:', result);
            }
        } catch (err) {
            console.error('Hashing failed:', err);
        }
    };

    const handleCopy = async () => {
        if (!hashedResult.value) return;
        
        const success = await copyToClipboard(hashedResult.value);
        if (success) {
            copied.value = true;
            setTimeout(() => copied.value = false, 2000);
        }
    };

    const incrementRounds = () => {
        if (rounds.value < 20) rounds.value++;
    };

    const decrementRounds = () => {
        if (rounds.value > 4) rounds.value--;
    };
</script>

<template>
    <div class="h-full bg-white/60 backdrop-blur-2xl border border-white/50 rounded-3xl shadow-xl shadow-indigo-500/5 p-6 flex flex-col justify-between transition-all duration-300">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="bg-indigo-500/10 p-2.5 rounded-2xl text-indigo-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800 leading-tight">Hash</h2>
                    <p class="text-xs font-semibold text-slate-400">Bcrypt Encryption</p>
                </div>
            </div>
        </div>

        <div class="space-y-6 flex-1">
            <!-- Password Input -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 pl-3">Input Text</label>
                <div class="relative group">
                    <input
                        v-model="password"
                        type="text"
                        placeholder="Enter text..."
                        class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl py-3 px-4 text-slate-700 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:border-indigo-500/50 focus:ring-4 focus:ring-indigo-500/10 transition-all text-sm font-medium shadow-sm"
                    />
                </div>
            </div>

            <!-- Rounds Control -->
            <div class="space-y-2">
                <div class="flex items-center justify-between px-1">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Cost (Rounds)</label>
                    <span class="bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-md text-[10px] font-bold border border-indigo-100">{{ rounds }}</span>
                </div>
                <div class="bg-white/40 border border-white/30 rounded-xl p-4">
                    <input
                        v-model.number="rounds"
                        type="range"
                        min="4"
                        max="20"
                        class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-indigo-500"
                    />
                    <div class="flex justify-between mt-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                        <span>Faster</span>
                        <span>Slower</span>
                    </div>
                </div>
            </div>

            <!-- Result -->
            <div v-if="hashedResult" class="animate-fade-in-up">
                 <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 pl-3 mb-1.5">Result</label>
                 <div class="relative group">
                    <div class="w-full bg-indigo-50/50 border-2 border-indigo-100 rounded-xl p-3 font-mono text-xs text-slate-600 break-all leading-relaxed hover:bg-white transition-colors shadow-inner">
                        {{ blockedHashedResult ? blockedHashedResult : hashedResult }}
                        <span v-if="blockedHashedResult">...</span>
                    </div>
                    <button
                        @click="handleCopy"
                        class="absolute top-2 right-2 p-1.5 bg-white rounded-lg shadow-sm text-indigo-500 hover:text-indigo-600 hover:scale-105 active:scale-95 transition-all border border-indigo-50"
                    >
                        <svg v-if="!copied" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                        <svg v-else class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </button>

                    <!-- Copy Popup -->
                    <Transition
                        enter-active-class="transform transition duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transform transition duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="copied" class="absolute inset-0 z-20 flex items-center justify-center bg-white/60 backdrop-blur-sm rounded-xl">
                            <div class="bg-emerald-500 text-white px-4 py-1.5 rounded-full font-bold text-sm shadow-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                Copied!
                            </div>
                        </div>
                    </Transition>
                 </div>
            </div>
        </div>

        <!-- Action Button -->
        <button
            @click="handleHash"
            :disabled="isLoading || !password"
            class="mt-6 w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-base rounded-[1rem] shadow-xl shadow-indigo-500/20 active:scale-[0.98] transition-all disabled:opacity-70 disabled:scale-100 disabled:cursor-not-allowed flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-indigo-500 group"
        >
             <div v-if="isLoading" class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
             <svg v-else class="w-5 h-5 opacity-90 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
             </svg>
             <span>{{ isLoading ? 'Hashing...' : 'Generate Hash' }}</span>
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
