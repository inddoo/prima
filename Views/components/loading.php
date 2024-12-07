<div id="loading-screen" class="fixed inset-0 bg-gradient-to-br from-indigo-500/90 via-purple-500/90 to-pink-500/90 backdrop-blur-sm z-50 flex items-center justify-center transition-all duration-300">
    <div class="text-center p-8 rounded-2xl">
        <!-- Loading Animation -->
        <div class="relative w-32 h-32 mx-auto">
            <!-- Outer spinning ring -->
            <div class="absolute inset-0 border-8 border-white/20 rounded-full animate-[spin_3s_linear_infinite]"></div>
            
            <!-- Middle spinning ring -->
            <div class="absolute inset-2 border-4 border-t-white/80 border-white/20 rounded-full animate-[spin_2s_linear_infinite_reverse]"></div>
            
            <!-- Inner spinning ring -->
            <div class="absolute inset-4 border-4 border-white/20 border-b-white/80 rounded-full animate-[spin_1s_linear_infinite]"></div>
            
            <!-- Center Logo -->
            <div class="absolute inset-0 flex items-center justify-center">
                <img src="/mpusbaru/assets/images/logo.png" 
                     alt="Loading" 
                     class="w-16 h-16 animate-pulse">
            </div>
        </div>

        <!-- Loading Text -->
        <div class="mt-8 relative">
            <div class="text-white text-xl font-bold tracking-wider uppercase relative z-10">
                <span class="inline-block animate-[bounce_1s_ease-in-out_infinite]">L</span>
                <span class="inline-block animate-[bounce_1s_ease-in-out_infinite] delay-[0.1s]">O</span>
                <span class="inline-block animate-[bounce_1s_ease-in-out_infinite] delay-[0.2s]">A</span>
                <span class="inline-block animate-[bounce_1s_ease-in-out_infinite] delay-[0.3s]">D</span>
                <span class="inline-block animate-[bounce_1s_ease-in-out_infinite] delay-[0.4s]">I</span>
                <span class="inline-block animate-[bounce_1s_ease-in-out_infinite] delay-[0.5s]">N</span>
                <span class="inline-block animate-[bounce_1s_ease-in-out_infinite] delay-[0.6s]">G</span>
            </div>
            
            <!-- Progress Dots -->
            <div class="mt-2 flex justify-center gap-1">
                <div class="w-2 h-2 rounded-full bg-white animate-[bounce_1s_ease-in-out_infinite]"></div>
                <div class="w-2 h-2 rounded-full bg-white animate-[bounce_1s_ease-in-out_infinite] delay-[0.2s]"></div>
                <div class="w-2 h-2 rounded-full bg-white animate-[bounce_1s_ease-in-out_infinite] delay-[0.4s]"></div>
            </div>
        </div>

        <!-- Random Tips -->
        <div class="mt-8 max-w-md text-white/80 text-sm animate-pulse">
            <div id="loading-tip" class="italic"></div>
        </div>
    </div>
</div>

<style>
@keyframes gradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.bg-gradient-animate {
    background-size: 200% 200%;
    animation: gradient 15s ease infinite;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loading = document.getElementById('loading-screen');
    const loadingTip = document.getElementById('loading-tip');
    
    // Random tips untuk ditampilkan
    const tips = [
        "Tahukah kamu? Membaca 20 menit sehari dapat meningkatkan kosakata!",
        "Tip: Atur pencahayaan yang cukup saat membaca",
        "Jangan lupa istirahat setiap 30 menit membaca ya!",
        "Membaca buku dapat meningkatkan kreativitas dan imajinasi",
        "Tip: Gunakan bookmark untuk menandai halaman terakhir",
        "Membaca sebelum tidur dapat membantu tidur lebih nyenyak"
    ];

    // Tampilkan tip random
    function showRandomTip() {
        const randomTip = tips[Math.floor(Math.random() * tips.length)];
        loadingTip.textContent = randomTip;
    }

    showRandomTip();
    // Ganti tip setiap 3 detik
    setInterval(showRandomTip, 3000);

    // Hide loading screen after content loads
    window.addEventListener('load', function() {
        loading.style.opacity = '0';
        setTimeout(() => {
            loading.style.display = 'none';
        }, 300);
    });

    // Show loading screen on navigation
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && !link.hasAttribute('data-no-loading')) {
            loading.style.display = 'flex';
            loading.style.opacity = '1';
        }
    });
});
</script> 