<?php
// Start session for auth tracking
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If the user is not logged in, redirect them straight to the login page!
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include top modular layout/header
include 'includes/header.php'; 
?>

    <!-- SECTION 1: Home -->
    <section id="home" class="min-h-screen flex items-center justify-center px-6 py-20 z-10 relative">
        <div class="max-w-4xl text-center">
            <div class="inline-block mb-4 px-4 py-1.5 rounded-full glass-card border border-purple-500/30 text-purple-300 text-xs font-code tracking-wider uppercase">
                // Welcome to your digital sanctuary
            </div>
            <h1 class="text-4xl md:text-6xl font-black tracking-wider mb-6 text-white font-robot uppercase">
                Code in <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400">Absolute Peace</span>
            </h1>
            <p class="text-lg md:text-xl text-indigo-200/80 mb-8 max-w-2xl mx-auto font-light">
                Designed specifically for IT students navigating late-night debugging sessions, complex algorithms, and endless terminal logs. Breathe, focus, and write clean code.
            </p>

            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                <a href="#breathing" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-medium shadow-lg shadow-purple-900/40 hover:opacity-90 transition-all text-center">
                    Explore Focus Tools
                </a>
                <a href="#about" class="w-full sm:w-auto px-8 py-3.5 rounded-xl glass-card text-indigo-200 hover:text-white hover:border-purple-400/50 transition-all text-center">
                    Discover The Vibe
                </a>
            </div>
        </div>
    </section>

    <!-- Cozy Mindset / Daily Fuel Widget -->
    <div style="max-width: 480px; margin: 3rem auto; padding: 2rem;" class="glass-card rounded-3xl border border-purple-500/30 text-center relative overflow-hidden shadow-2xl font-code z-10">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-purple-500/10 rounded-full blur-2xl pointer-events-none"></div>
        
        <!-- Top Header Row -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.50rem;">
            <span class="text-xs uppercase font-bold tracking-wider text-purple-300">// Cozy Mindset Fuel</span>
            <span class="text-[10px] text-emerald-400 bg-emerald-950/60 px-3 py-1 rounded-full border border-emerald-500/30">Daily Sanity</span>
        </div>

        <!-- Quote Display Area -->
        <blockquote id="quoteText" style="margin-bottom: 1.75rem;" class="text-sm md:text-base text-indigo-100 font-light italic min-h-[70px] flex items-center justify-center leading-relaxed">
            "It's not a bug, it's an undocumented feature. Take a deep breath and refactor."
        </blockquote>

        <!-- Shuffle Button with safe spacing -->
        <button id="shuffleQuoteBtn" style="width: 100%; padding: 0.85rem 1rem;" class="bg-gradient-to-r from-purple-600/50 to-indigo-600/50 hover:from-purple-600 hover:to-indigo-600 text-purple-100 hover:text-white rounded-xl text-xs font-bold transition-all border border-purple-500/40 cursor-pointer shadow-lg uppercase tracking-wider">
            Draw Another Quote
        </button>
    </div>

    <!-- SECTION 2: Breathing Tool -->
    <section id="breathing" class="min-h-screen flex items-center justify-center px-6 py-20 z-10 relative border-t border-indigo-900/30">
        <div class="max-w-2xl w-full mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold mb-3 text-white">IT Student Focus Services</h1>
                <p class="text-indigo-200/70 font-light">Interactive utilities engineered to clear mental cache and boost programming flow states.</p>
            </div>
            <div class="glass-card p-8 rounded-2xl flex flex-col items-center justify-between">
                <div class="text-center w-full mb-6">
                    <h2 class="text-xl font-semibold text-purple-300 mb-2 font-code">01 // Compile Breath (Physiological Sigh)</h2>
                    <p class="text-sm text-indigo-200/70">Deep double-inhale followed by a long release to quickly melt away stress.</p>
                </div>
                <div id="breathCircle" class="w-36 h-36 rounded-full border-4 border-purple-400/50 bg-purple-900/20 flex items-center justify-center text-sm font-code text-purple-200 mb-6 shadow-inner shadow-purple-500/25 scale-100">
                    Ready
                </div>
                <div class="flex items-center gap-3 w-full justify-center">
                    <button id="breathBtn" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-sm font-medium transition-all shadow-lg shadow-purple-900/30 cursor-pointer">
                        Start Breathing
                    </button>
                    <button id="restartBreathBtn" class="hidden px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-indigo-200 rounded-xl text-sm font-medium transition-all border border-indigo-900 cursor-pointer">
                        Restart
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 3: Zen Syntax Typing Game with Personal Best -->
    <section id="typing" class="min-h-screen flex items-center justify-center px-6 py-20 z-10 relative border-t border-indigo-900/30">
        <div class="max-w-2xl w-full mx-auto text-center">
            <h2 class="text-3xl font-light mb-2 text-white">Zen Code Typing</h2>
            <p class="text-indigo-200/70 font-light mb-4">No ticking clock. No red squiggles. Just muscle memory and flow. Type right on the snippet below.</p>
            
            <!-- Personal Best Record Badge -->
            <div class="mb-6 inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-indigo-900/40 border border-purple-500/30 text-xs font-code text-purple-300">
                <span>🏆 Personal Best:</span> <span id="personalBestDisplay" class="text-emerald-400 font-bold">0 WPM</span>
            </div>

            <div class="mb-6 flex gap-2">
                <input type="text" id="customSnippetInput" placeholder="Paste or type your own custom code snippet here..." class="w-full bg-slate-900/80 border border-indigo-900/60 rounded-xl px-4 py-2.5 text-xs font-code text-indigo-100 focus:outline-none focus:border-purple-500">
                <button id="loadCustomSnippetBtn" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-code transition-all shrink-0 cursor-pointer">Load Custom</button>
            </div>

            <div class="glass-card p-8 rounded-2xl text-left shadow-2xl relative overflow-hidden transition-all duration-300 border border-indigo-500/25 outline-none cursor-text flex flex-col min-h-[250px]" id="typingCard" tabindex="0">
                
                <div class="flex items-center space-x-2 mb-6">
                    <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                    <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                </div>

                <div id="codeDisplay" class="font-code text-base md:text-lg text-indigo-200 mb-6 leading-relaxed whitespace-pre font-medium select-none text-left pl-2 overflow-x-auto flex-grow"></div>

                <div class="text-xs font-code flex justify-between items-center text-indigo-300/60 mt-auto">
                    <span id="cardStatusText">💡 Click card to activate typing...</span>
                    <span id="nextKeyHint" class="text-amber-300/80 font-bold"></span>
                    <button id="resetTypeBtn" class="text-purple-400 hover:underline cursor-pointer">Reset Snippet</button>
                </div>

                <!-- Completion Overlay with Stats -->
                <div id="completionOverlay" class="absolute inset-0 w-full h-full bg-slate-950/90 backdrop-blur-md flex flex-col items-center justify-center text-center z-30 hidden">
                    <h3 class="text-2xl md:text-3xl font-bold text-emerald-400 mb-2 font-code tracking-wide">Snippet Completed! 🎉</h3>
                    <div class="flex space-x-6 mb-6 font-code text-sm">
                        <div class="bg-slate-900 px-4 py-2 rounded-lg border border-indigo-900">
                            <span class="text-indigo-400 block text-xs uppercase mb-1">Speed</span>
                            <span class="text-white text-lg"><span id="wpmDisplay">0</span> WPM</span>
                        </div>
                        <div class="bg-slate-900 px-4 py-2 rounded-lg border border-indigo-900">
                            <span class="text-indigo-400 block text-xs uppercase mb-1">Accuracy</span>
                            <span class="text-white text-lg"><span id="accuracyDisplay">0</span>%</span>
                        </div>
                    </div>
                    <button id="nextSnippetBtn" class="px-6 py-2.5 rounded-full bg-emerald-600/20 border border-emerald-400/50 text-emerald-300 font-code text-sm hover:bg-emerald-600/40 transition-all shadow-lg cursor-pointer">
                        Load Next (Reset)
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 4: About -->
    <section id="about" class="min-h-screen flex items-center justify-center px-6 py-20 z-10 relative border-t border-indigo-900/30">
        <div class="max-w-4xl mx-auto w-full">
            <div class="glass-card p-8 md:p-12 rounded-3xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <h1 class="text-3xl md:text-4xl font-bold mb-6 text-white font-robot">The Philosophy Behind <span class="text-purple-400">Cozy Syntax</span></h1>
                <p class="text-indigo-200/80 mb-6 font-light leading-relaxed">
                    Coding architecture doesn't have to feel cold, sterile, or exhausting. As IT students juggling data structures, backend pipelines, and software design principles, our workspace environment matters just as much as our logic.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 my-8">
                    <div class="bg-slate-900/50 p-5 rounded-xl border border-indigo-900/40">
                        <h3 class="font-code text-purple-300 text-sm mb-2">01 // Purpose</h3>
                        <p class="text-xs text-indigo-200/70">To create a comfortable and inspiring coding environment that enhances productivity and reduces stress.</p>
                    </div>
                    <div class="bg-slate-900/50 p-5 rounded-xl border border-indigo-900/40">
                        <h3 class="font-code text-purple-300 text-sm mb-2">02 // Architecture</h3>
                        <p class="text-xs text-indigo-200/70">Built on database persistence and modern Tailwind compilation workflows rather than fragile CDN scripts.</p>
                    </div>
                    <div class="bg-slate-900/50 p-5 rounded-xl border border-indigo-900/40">
                        <h3 class="font-code text-purple-300 text-sm mb-2">03 // Mindfulness</h3>
                        <p class="text-xs text-indigo-200/70">Incorporating wellness modules to help developers maintain mental clarity under tight deadlines.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACT SECTION (Ping The Server - Dedicated Endpoint AJAX POST) -->
    <section id="contact" class="min-h-screen flex items-center justify-center px-6 py-20 relative z-10 border-t border-indigo-900/30">
        <div class="max-w-2xl w-full mx-auto glass-card p-8 rounded-3xl shadow-2xl border border-indigo-900/40 bg-slate-950/40 backdrop-blur-xl">
            <div class="text-center mb-6">
                <h2 class="text-3xl font-bold mb-2 text-white tracking-wider">Ping The Server</h2>
                <p class="text-indigo-300 font-light text-sm">Leave anonymous feedback, feature requests, or just drop a cozy hello.</p>
            </div>
            
            <!-- Dynamic Database feedback alert banner -->
            <div id="contactAlert" class="hidden mb-6 p-4 rounded-xl text-sm font-code text-center backdrop-blur-md"></div>

            <form id="contactForm" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-code font-bold uppercase text-purple-300">Your Handle / Alias</label>
                        <input type="text" name="alias" id="contactAlias" required placeholder="e.g. CodeExplorer" class="w-full bg-slate-950/60 border border-indigo-900/50 rounded-xl px-4 py-2.5 text-sm text-indigo-100 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all font-code shadow-inner">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-code font-bold uppercase text-purple-300">Current Status (Optional)</label>
                        <input type="text" name="status_text" placeholder="e.g. Debugging late at night" class="w-full bg-slate-950/60 border border-indigo-900/50 rounded-xl px-4 py-2.5 text-sm text-indigo-100 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all font-code shadow-inner">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-code font-bold uppercase text-purple-300">Message / Log Entry</label>
                    <textarea name="message" id="contactMessage" rows="4" required placeholder="Type something cozy..." class="w-full bg-slate-950/60 border border-indigo-900/50 rounded-xl px-4 py-3 text-sm text-indigo-100 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all font-code resize-none shadow-inner"></textarea>
                </div>
                <button type="submit" id="contactSubmitBtn" class="w-full py-3.5 mt-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-sm font-bold shadow-lg shadow-purple-500/20 cursor-pointer font-code uppercase tracking-wider transition-all">
                    Transmit Log
                </button>
            </form>
        </div>
    </section>

    <!-- AJAX Script pointing to process_contact.php -->
    <script>
    $(document).ready(function() {
        $('#contactSubmitBtn').off('click').on('click', function(e) {
            e.preventDefault(); // Completely stop form redirect/reload
            
            const form = $('#contactForm');
            const btn = $(this);
            
            const alias = $('#contactAlias').val();
            const message = $('#contactMessage').val();
            
            if (!alias || !message) {
                $('#contactAlert')
                    .removeClass('hidden bg-emerald-900/40 border-emerald-500/30 text-emerald-300')
                    .addClass('bg-rose-900/40 border border-rose-500/30 text-rose-300')
                    .text('// Error: Please fill in your handle and message.');
                return;
            }

            btn.text('Transmitting...').prop('disabled', true);

            $.ajax({
                url: 'process_contact.php', // Points to our clean, isolated backend script
                type: 'POST',
                dataType: 'json',
                data: form.serialize(),
                success: function(res) {
                    $('#contactAlert')
                        .removeClass('hidden bg-rose-900/40 border-rose-500/30 text-rose-300 bg-emerald-900/40 border-emerald-500/30 text-emerald-300')
                        .addClass(res.status === 'success' ? 'bg-emerald-900/40 border border-emerald-500/30 text-emerald-300' : 'bg-rose-900/40 border border-rose-500/30 text-rose-300')
                        .text(res.message);
                    
                    if (res.status === 'success') {
                        form[0].reset();
                    }
                    btn.text('Transmit Log').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    $('#contactAlert')
                        .removeClass('hidden bg-emerald-900/40 border-emerald-500/30 text-emerald-300')
                        .addClass('bg-rose-900/40 border border-rose-500/30 text-rose-300')
                        .text('// Transmission error. Check console for details.');
                    btn.text('Transmit Log').prop('disabled', false);
                }
            });
        });
    });
    </script>

<?php 
// Include modular footer layout
include 'includes/footer.php'; 
?>