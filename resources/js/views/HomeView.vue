<script setup>
import PasswordGenerator from '../components/PasswordGenerator.vue';
import PasswordHasher from '../components/PasswordHasher.vue';
import PasswordVerifier from '../components/PasswordVerifier.vue';
</script>

<template>
    <div class="min-h-screen bg-[#F5F5F7] font-sans selection:bg-blue-500/20 selection:text-blue-900 overflow-x-hidden relative">
        <!-- Subtle Animated Mesh Background -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none opacity-60">
            <div class="absolute top-[-20%] left-[-10%] w-[70vw] h-[70vw] rounded-full bg-blue-300/20 blur-[100px] animate-blob mix-blend-multiply"></div>
            <div class="absolute top-[10%] right-[-20%] w-[60vw] h-[60vw] rounded-full bg-indigo-300/20 blur-[100px] animate-blob animation-delay-2000 mix-blend-multiply"></div>
            <div class="absolute bottom-[-20%] left-[20%] w-[60vw] h-[60vw] rounded-full bg-emerald-300/20 blur-[100px] animate-blob animation-delay-4000 mix-blend-multiply"></div>
        </div>

        <!-- Floating Navbar -->
        <nav class="sticky top-4 z-50 mx-auto max-w-6xl px-4">
            <div class="bg-white/80 backdrop-blur-md border border-white/60 shadow-sm rounded-2xl px-5 py-3 flex items-center justify-between transition-all">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center shadow-md shadow-blue-500/20">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold tracking-tight text-slate-800">HashTool</span>
                </div>
                
                <div class="flex items-center gap-4">
                    <a href="https://github.com/ashikurweb" target="_blank" class="group flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100/50 hover:bg-slate-100 transition-all">
                        <svg class="w-4 h-4 text-slate-500 group-hover:text-slate-900 transition-colors" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                        <span class="text-xs font-semibold text-slate-500 group-hover:text-slate-900">@ashikurweb</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="relative z-10 max-w-6xl mx-auto px-4 py-12">
            <!-- Hero -->
            <div class="text-center max-w-2xl mx-auto mb-12 animate-fade-in-up">
                <h1 class="text-4xl md:text-5xl font-black tracking-tight text-slate-900 mb-4">
                    Security, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Simplifed</span>.
                </h1>
                <p class="text-lg text-slate-500 font-medium leading-relaxed">
                    A modern suite of crypto tools. Secure generation, hashing, and verification.
                </p>
            </div>

            <!-- Bento Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-start">
                <PasswordGenerator />
                <PasswordHasher />
                <PasswordVerifier />
            </div>

            <!-- Features Cards (Compact Glass) -->
            <div class="mt-16 grid md:grid-cols-3 gap-6 text-sm">
                <div class="bg-white/40 backdrop-blur-md border border-white/50 p-6 rounded-2xl hover:bg-white/50 transition-all">
                    <div class="flex items-center gap-3 mb-2">
                         <div class="w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <h3 class="font-bold text-slate-800">Secure Core</h3>
                    </div>
                    <p class="text-slate-500 leading-relaxed">Powered by standard Web Crypto API for true entropy generation.</p>
                </div>
                <div class="bg-white/40 backdrop-blur-md border border-white/50 p-6 rounded-2xl hover:bg-white/50 transition-all">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-indigo-500/10 rounded-lg flex items-center justify-center text-indigo-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <h3 class="font-bold text-slate-800">Instant Speed</h3>
                    </div>
                    <p class="text-slate-500 leading-relaxed">Optimized algorithms run entirely in your browser with zero latency.</p>
                </div>
                <div class="bg-white/40 backdrop-blur-md border border-white/50 p-6 rounded-2xl hover:bg-white/50 transition-all">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center text-emerald-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h3 class="font-bold text-slate-800">Verified</h3>
                    </div>
                    <p class="text-slate-500 leading-relaxed">Real-time matching against standard bcrypt hashes with cost factor support.</p>
                </div>
            </div>
        </main>

        <footer class="relative z-10 py-10 text-center">
            <p class="text-xs text-slate-400 font-medium tracking-wide">
                Made with ❤️ by <a href="https://github.com/ashikurweb" class="text-blue-500 hover:text-blue-600 transition-colors">ashikurweb</a>
            </p>
        </footer>
    </div>
</template>

<style scoped>
@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob {
    animation: blob 10s infinite;
}
.animation-delay-2000 {
    animation-delay: 2s;
}
.animation-delay-4000 {
    animation-delay: 4s;
}
@keyframes fade-in-up {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
}
</style>
