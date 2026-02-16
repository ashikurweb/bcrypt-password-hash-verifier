<script setup>
import { ref, computed } from 'vue';
import { usePassword } from '../composables/usePassword.js';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue', 'generated']);

const { generatePassword, copyToClipboard, isLoading, error } = usePassword();

const config = ref({
    length: 16,
    include_uppercase: true,
    include_lowercase: true,
    include_numbers: true,
    include_special_characters: true,
});

const generatedPassword = ref('');
const strength = ref(null);
const copied = ref(false);

const strengthScore = computed(() => strength.value?.score || 0);

const strengthColor = computed(() => {
    if (!strength.value) return 'bg-slate-200';
    const s = strength.value.strength;
    if (s === 'strong') return 'bg-emerald-500 shadow-[0_0_12px_rgba(16,185,129,0.4)]';
    if (s === 'medium') return 'bg-amber-500 shadow-[0_0_12px_rgba(245,158,11,0.4)]';
    return 'bg-rose-500 shadow-[0_0_12px_rgba(244,63,94,0.4)]';
});

const strengthLabel = computed(() => {
    if (!strength.value) return 'Unknown';
    return strength.value.strength.charAt(0).toUpperCase() + strength.value.strength.slice(1);
});

const canGenerate = computed(() => {
    return config.value.include_uppercase ||
           config.value.include_lowercase ||
           config.value.include_numbers ||
           config.value.include_special_characters;
});

const handleGenerate = async () => {
    if (!canGenerate.value) return;

    try {
        const result = await generatePassword(config.value);
        generatedPassword.value = result.data.password;
        strength.value = result.data.strength;
        emit('update:modelValue', generatedPassword.value);
        emit('generated', generatedPassword.value);
    } catch (err) {
        console.error('Generation failed:', err);
    }
};

const handleCopy = async () => {
    if (!generatedPassword.value) return;
    const success = await copyToClipboard(generatedPassword.value);
    if (success) {
        copied.value = true;
        setTimeout(() => copied.value = false, 2000);
    }
};

// Generate on mount
handleGenerate();
</script>

<template>
    <div class="h-full bg-white/60 backdrop-blur-2xl border border-white/50 rounded-3xl shadow-xl shadow-blue-500/5 p-6 flex flex-col justify-between transition-all duration-300">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="bg-blue-500/10 p-2.5 rounded-2xl text-blue-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800 leading-tight">Generate</h2>
                    <p class="text-xs font-semibold text-slate-400">Secure Passwords</p>
                </div>
            </div>
        </div>

        <div class="space-y-6 flex-1">
            <div class="relative group">
                <div class="relative bg-slate-50 rounded-xl flex items-center shadow-inner border-2 border-slate-200 overflow-hidden group-hover:border-blue-300/50 transition-colors">
                    <input
                        type="text"
                        readonly
                        :value="generatedPassword"
                        class="w-full bg-transparent py-3.5 pl-4 pr-12 font-mono text-lg text-slate-700 tracking-wide focus:outline-none placeholder:text-slate-400 font-medium"
                        placeholder="..."
                    />
                    <button
                        @click="handleCopy"
                        :disabled="!generatedPassword"
                        class="absolute right-1.5 p-2 bg-white border border-slate-200 hover:border-blue-300 rounded-lg text-slate-400 hover:text-blue-600 shadow-sm transition-all disabled:opacity-50 disabled:shadow-none"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                    </button>
                </div>

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

            <!-- Strength Dots -->
            <div class="flex gap-1.5 h-1.5">
                <div v-for="i in 4" :key="i" 
                    class="flex-1 rounded-full transition-all duration-500"
                    :class="[
                        i <= (strengthScore / 25) 
                        ? (strengthScore > 70 ? 'bg-emerald-400' : (strengthScore > 40 ? 'bg-amber-400' : 'bg-rose-400'))
                        : 'bg-slate-200/50'
                    ]"
                ></div>
            </div>

            <!-- Controls -->
            <div class="space-y-4">
                <!-- Length Slider -->
                <div class="bg-white/40 rounded-xl p-4 border border-white/30">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wide">Length</span>
                        <span class="text-sm font-black text-slate-800 bg-white/50 px-2 py-0.5 rounded-md shadow-sm">{{ config.length }}</span>
                    </div>
                    <input
                        type="range"
                        v-model.number="config.length"
                        min="8"
                        max="64"
                        class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-500"
                    />
                </div>

                <!-- Toggles Grid -->
                <!-- Toggles List (iOS Switches) -->
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center justify-between p-3 bg-white/50 rounded-xl border border-slate-200 cursor-pointer hover:bg-white/80 transition-all group">
                        <span class="text-xs font-bold text-slate-600">Uppercase</span>
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="config.include_uppercase" class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600 shadow-inner"></div>
                        </div>
                    </label>
                    <label class="flex items-center justify-between p-3 bg-white/50 rounded-xl border border-slate-200 cursor-pointer hover:bg-white/80 transition-all group">
                        <span class="text-xs font-bold text-slate-600">Lowercase</span>
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="config.include_lowercase" class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600 shadow-inner"></div>
                        </div>
                    </label>
                    <label class="flex items-center justify-between p-3 bg-white/50 rounded-xl border border-slate-200 cursor-pointer hover:bg-white/80 transition-all group">
                        <span class="text-xs font-bold text-slate-600">Numbers</span>
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="config.include_numbers" class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600 shadow-inner"></div>
                        </div>
                    </label>
                    <label class="flex items-center justify-between p-3 bg-white/50 rounded-xl border border-slate-200 cursor-pointer hover:bg-white/80 transition-all group">
                        <span class="text-xs font-bold text-slate-600">Symbols</span>
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="config.include_special_characters" class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600 shadow-inner"></div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <button
            @click="handleGenerate"
            :disabled="isLoading || !canGenerate"
            class="mt-6 w-full py-4 bg-slate-900 hover:bg-slate-800 text-white font-bold text-base rounded-[1rem] shadow-xl shadow-slate-900/20 active:scale-[0.98] transition-all disabled:opacity-70 disabled:scale-100 disabled:cursor-not-allowed flex items-center justify-center gap-2 group"
        >
             <div v-if="isLoading" class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
             <svg v-else class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
             </svg>
             <span>{{ isLoading ? 'Generating...' : 'Generate Password' }}</span>
        </button>
        
        <p v-if="error" class="mt-3 text-center text-[10px] text-rose-500 font-bold bg-rose-50/50 p-2 rounded-lg border border-rose-100">
            {{ error }}
        </p>
    </div>
</template>
