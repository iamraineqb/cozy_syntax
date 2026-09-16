<?php
// Ensure session is started for auth tracking
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$is_auth_page = ($current_page === 'login.php' || $current_page === 'register.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cozy Syntax - The IT Student Sanctuary</title>
    <!-- Compiled Tailwind Output -->
    <link href="./dist/output.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="./src/input.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500&family=Orbitron:wght@500;700;900&family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- jQuery Library -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Smooth Page Load Transition (Fixed: Removed transform so it doesn't break sticky/fixed layouts) -->
    <style>
        body {
            animation: fadeIn 0.3s ease-in forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body class="m-0 bg-slate-950 text-indigo-100 relative overflow-x-hidden selection:bg-purple-500 selection:text-white">

   <!-- Animated Background Themes (Forced Fixed Position) -->
    <div id="theme1" class="bg-theme active" style="position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; z-index: -50 !important; pointer-events: none !important;"></div>
    <div id="theme2" class="bg-theme" style="position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; z-index: -50 !important; pointer-events: none !important;"></div>
    <div id="theme3" class="bg-theme" style="position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; z-index: -50 !important; pointer-events: none !important;"></div>
    <canvas id="particleCanvas" style="position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; z-index: -40 !important; pointer-events: none !important;"></canvas>

    <!-- Audio Streams & Alarm Sound (Only loaded if not on auth pages) -->
    <?php if (!$is_auth_page): ?>
    <audio id="audioLofi" loop src="https://commondatastorage.googleapis.com/codeskulptor-assets/Epoq-Lepidoptera.ogg"></audio>
    <audio id="audioRain" loop src="https://actions.google.com/sounds/v1/weather/rain_heavy_loud.ogg"></audio>
    <audio id="audioCosmic" loop src="https://actions.google.com/sounds/v1/ricochet.ogg"></audio>
    <audio id="alarmSound" src="https://actions.google.com/sounds/v1/alarms/digital_watch_alarm_long.ogg" preload="auto"></audio>
    <?php endif; ?>

    <!-- Navigation Bar (Removed 'relative' so 'sticky' works perfectly) -->
    <nav class="w-full px-6 py-4 flex items-center justify-between glass-card border-b border-indigo-900/40 z-50 sticky top-0">
        
        <!-- Left: Logo (Takes exactly 1/3 of the space) -->
        <div class="flex items-center z-10 w-1/3">
            <a href="index.php#home" class="text-purple-400 font-code text-xl font-bold cursor-pointer">&lt;CozySyntax/&gt;</a>
        </div>
        
        <!-- Center: Nav Links Perfectly Centered (Takes exactly 1/3 of the space) -->
        <?php if (!$is_auth_page): ?>
            <div class="hidden md:flex items-center justify-center space-x-8 text-sm font-medium w-1/3">
                <a href="index.php#home" class="text-indigo-300 hover:text-purple-400 transition-colors">Home</a>
                <a href="index.php#breathing" class="text-indigo-300 hover:text-purple-400 transition-colors">Tools</a>
                <a href="index.php#about" class="text-indigo-300 hover:text-purple-400 transition-colors">About</a>
                <a href="index.php#contact" class="text-indigo-300 hover:text-purple-400 transition-colors">Contact</a>
            </div>
        <?php else: ?>
            <div class="w-1/3"></div>
        <?php endif; ?>

        <!-- Right: Widgets & Profile (Takes exactly 1/3 of the space) -->
        <div class="flex items-center justify-end space-x-3 z-10 w-1/3">
            <?php if (!$is_auth_page): ?>
                <!-- Pomodoro Dropdown Trigger -->
                <div class="relative hidden sm:block">
                    <button id="pomoMenuBtn" class="flex items-center space-x-2 px-3 py-1.5 rounded-full glass-card border border-purple-500/30 text-xs font-code cursor-pointer hover:border-purple-400 transition-all">
                        <span id="navPomoTime" class="text-emerald-400 font-bold">25:00</span>
                    </button>
                    <div id="pomoDropdown" class="hidden absolute right-0 mt-2 w-64 glass-card rounded-2xl shadow-2xl p-4 z-50 text-xs font-code space-y-4">
                        <div class="font-bold text-purple-300 border-b border-indigo-900 pb-1 flex justify-between items-center">
                            <span>Pomodoro Timer</span>
                            <span class="text-[10px] text-indigo-400">[Ctrl+Space]</span>
                        </div>
                        <div class="flex justify-center gap-2">
                            <button id="pomoWorkBtn" class="px-3 py-1.5 rounded-lg text-xs font-code bg-purple-600 text-white shadow-md transition-all cursor-pointer">Work (25m)</button>
                            <button id="pomoRestBtn" class="px-3 py-1.5 rounded-lg text-xs font-code glass-card text-indigo-200 hover:text-white transition-all cursor-pointer">Rest (5m)</button>
                        </div>
                        <div id="pomoDisplay" class="text-4xl font-bold font-code text-center text-emerald-400 tracking-wider">
                            25:00
                        </div>
                        <div class="flex items-center justify-center gap-2">
                            <button id="pomoToggleBtn" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-lg text-xs font-medium transition-all shadow-md cursor-pointer">Start</button>
                            <button id="pomoResetBtn" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-indigo-200 rounded-lg text-xs font-medium transition-all border border-indigo-900 cursor-pointer">Reset</button>
                        </div>
                    </div>
                </div>

                <!-- Audio Dropdown Menu -->
                <div class="relative">
                    <button id="soundMenuBtn" class="flex items-center space-x-2 px-3 py-1.5 rounded-full glass-card hover:border-purple-400 transition-all cursor-pointer">
                        <div id="equalizer" class="flex items-end space-x-0.5 h-3 w-3">
                            <span class="w-0.5 bg-emerald-400 rounded-t eq-bar-1"></span>
                            <span class="w-0.5 bg-emerald-400 rounded-t eq-bar-2"></span>
                            <span class="w-0.5 bg-emerald-400 rounded-t eq-bar-3"></span>
                        </div>
                        <span id="currentSoundLabel" class="text-xs font-code text-emerald-300 hidden sm:inline">Audio: Lofi</span>
                    </button>
                    <div id="soundDropdown" class="hidden absolute right-0 mt-2 w-56 glass-card rounded-xl shadow-2xl p-2 z-50 text-xs font-code space-y-1">
                        <button data-sound="lofi" class="w-full text-left px-3 py-2 rounded-lg hover:bg-purple-600/30 text-indigo-200 transition-colors cursor-pointer">Lofi Chill Beats</button>
                        <button data-sound="rain" class="w-full text-left px-3 py-2 rounded-lg hover:bg-purple-600/30 text-indigo-200 transition-colors cursor-pointer">Relaxing Rain</button>
                        <button data-sound="cosmic" class="w-full text-left px-3 py-2 rounded-lg hover:bg-purple-600/30 text-indigo-200 transition-colors cursor-pointer">Ambient Cosmic</button>
                        <button data-sound="off" class="w-full text-left px-3 py-2 rounded-lg hover:bg-rose-500/20 text-rose-300 transition-colors border-t border-indigo-900/50 mt-1 cursor-pointer">🔇 Mute Audio</button>
                        
                        <div class="px-3 py-2 border-t border-indigo-900/50 mt-2 flex flex-col space-y-2">
                            <label for="volumeSlider" class="text-indigo-300">Volume</label>
                            <input type="range" id="volumeSlider" min="0" max="1" step="0.05" value="0.5" class="w-full cursor-pointer">
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Circle Profile Avatar & Dropdown -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="relative">
                   <button id="profileMenuBtn" style="width: 36px; height: 36px; min-width: 36px; min-height: 36px;" class="rounded-full overflow-hidden border-2 border-emerald-500/40 hover:border-emerald-400 transition-all cursor-pointer shadow-lg flex items-center justify-center bg-slate-900 p-0 focus:outline-none">
    <img id="navAvatarImg" src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=100&h=100&fit=crop&crop=faces" alt="Avatar" style="width: 36px; height: 36px; object-fit: cover;" class="block">
</button>
                    
                    <!-- Profile Dropdown Card -->
                    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-64 glass-card rounded-2xl shadow-2xl p-4 z-50 text-xs font-code space-y-4">
                        <div class="font-bold text-emerald-400 border-b border-indigo-900 pb-3 text-center text-sm tracking-wide">
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </div>

                        <!-- Upload New Image with proper spacing -->
                        <div class="space-y-2 pt-1">
                            <label class="block text-[11px] font-semibold text-indigo-300">Profile Picture</label>
                            <label for="avatarUploadInput" class="w-full block text-center py-2.5 px-3 bg-purple-600/40 hover:bg-purple-600 text-purple-100 rounded-xl cursor-pointer transition-all border border-purple-500/40 font-bold text-[11px] shadow-sm">
                                ✨ Upload New Image
                            </label>
                            <input type="file" id="avatarUploadInput" accept="image/*" class="hidden">
                        </div>

                        <!-- Theme Switcher with Active Highlighting Defaulted on Cosmos -->
                        <div class="space-y-2 border-t border-indigo-900/50 pt-3">
                            <span class="block text-[11px] font-semibold text-indigo-300 mb-1">Atmosphere Theme</span>
                            <div class="grid grid-cols-3 gap-2">
                                <button id="themeBtn1" onclick="setTheme('theme1')" class="theme-option-btn py-2 px-1 bg-purple-600 border-2 border-emerald-400 shadow-lg shadow-purple-500/40 text-white text-[11px] rounded-xl transition-all cursor-pointer font-bold text-center">Cosmos</button>
                                <button id="themeBtn2" onclick="setTheme('theme2')" class="theme-option-btn py-2 px-1 bg-slate-900/60 hover:bg-blue-600/40 text-[11px] rounded-xl text-indigo-100 transition-all cursor-pointer font-bold text-center border border-indigo-900">Ocean</button>
                                <button id="themeBtn3" onclick="setTheme('theme3')" class="theme-option-btn py-2 px-1 bg-slate-900/60 hover:bg-indigo-600/40 text-[11px] rounded-xl text-indigo-100 transition-all cursor-pointer font-bold text-center border border-indigo-900">Nebula</button>
                            </div>
                        </div>

                        <a href="logout.php" class="w-full block text-center py-2.5 px-3 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 rounded-xl transition-colors border border-rose-500/30 font-bold tracking-wide mt-3">
                            Logout System
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex items-center space-x-2 font-code text-xs">
                    <?php if ($current_page === 'login.php'): ?>
                        <span class="text-indigo-400">Need an account?</span>
                        <a href="register.php" class="px-3 py-1.5 rounded-lg bg-purple-600 text-white transition-all shadow-md">Sign Up</a>
                    <?php elseif ($current_page === 'register.php'): ?>
                        <span class="text-indigo-400">Have an account?</span>
                        <a href="login.php" class="px-3 py-1.5 rounded-lg glass-card text-indigo-300 hover:text-white transition-all">Login</a>
                    <?php else: ?>
                        <a href="login.php" class="px-3 py-1.5 rounded-lg glass-card text-indigo-300 hover:text-white transition-all">Login</a>
                        <a href="register.php" class="px-3 py-1.5 rounded-lg bg-purple-600 text-white transition-all shadow-md">Sign Up</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!$is_auth_page): ?>
                <button id="mobileMenuBtn" class="md:hidden text-indigo-300 hover:text-purple-400 cursor-pointer p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
            <?php endif; ?>
        </div>
    </nav>