import { ref, computed } from 'vue';
import { passwordApi } from '../services/api.js';

export function usePassword() {
    const loading = ref(false);
    const error = ref(null);

    const generatedPassword = ref('');
    const passwordStrength = ref(null);

    const hashedPassword = ref('');
    const verificationResult = ref(null);

    const isLoading = computed(() => loading.value);
    const hasError = computed(() => error.value !== null);

    /**
     * Calculate password strength on client-side
     */
    const calculateStrength = (password) => {
        if (!password) return { score: 0, strength: 'weak' };

        let score = 0;
        const length = password.length;

        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumbers = /[0-9]/.test(password);
        const hasSpecial = /[^A-Za-z0-9]/.test(password);

        const charTypes = [hasUppercase, hasLowercase, hasNumbers, hasSpecial].filter(Boolean).length;

        // Base score from length
        if (length < 8) {
            score = Math.min(20, length * 2);
        } else if (length < 12) {
            score = 30 + (length - 8) * 5;
        } else {
            score = 50 + Math.min(30, (length - 12) * 2);
        }

        // Bonus for character variety
        score += charTypes * 10;

        // Cap at 100
        score = Math.min(100, score);

        let strength = 'weak';
        if (score > 70) strength = 'strong';
        else if (score > 40) strength = 'medium';

        return { score, strength };
    };

    /**
     * Generate password via API
     */
    const generatePassword = async (config) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await passwordApi.generate(config);
            generatedPassword.value = response.data.data.password;
            passwordStrength.value = response.data.data.strength;
            return response.data;
        } catch (err) {
            error.value = err.message || 'Failed to generate password';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Hash password via API
     */
    const hashPassword = async (password, rounds = 10) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await passwordApi.hash(password, rounds);
            hashedPassword.value = response.data.data.hashed_password;
            return response.data;
        } catch (err) {
            error.value = err.message || 'Failed to hash password';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Verify password via API
     */
    const verifyPassword = async (password, hash) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await passwordApi.verify(password, hash);
            verificationResult.value = response.data.data.is_valid;
            return response.data;
        } catch (err) {
            error.value = err.message || 'Failed to verify password';
            verificationResult.value = null;
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Copy text to clipboard
     */
    const copyToClipboard = async (text) => {
        if (!text) return false;

        try {
            // Try modern API first
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(text);
                return true;
            } else {
                // Fallback for older browsers or non-secure contexts
                const textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";
                textArea.style.left = "-9999px";
                textArea.style.top = "0";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                const successful = document.execCommand('copy');
                document.body.removeChild(textArea);
                return successful;
            }
        } catch (err) {
            console.error('Failed to copy:', err);
            return false;
        }
    };

    /**
     * Clear all state
     */
    const clearState = () => {
        generatedPassword.value = '';
        passwordStrength.value = null;
        hashedPassword.value = '';
        verificationResult.value = null;
        error.value = null;
    };

    return {
        // State
        loading,
        error,
        generatedPassword,
        passwordStrength,
        hashedPassword,
        verificationResult,

        // Computed
        isLoading,
        hasError,

        // Methods
        generatePassword,
        hashPassword,
        verifyPassword,
        calculateStrength,
        copyToClipboard,
        clearState,
    };
}
